<?php

namespace Tests\Feature;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\PaymentResource;
use App\Jobs\SendLoginAlertJob;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Services\SystemErrorTranslator;
use App\Services\SystemNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    protected Role $superAdminRole;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    /**
     * Test booking resource badge reflects pending bookings count.
     */
    public function test_booking_resource_badge_reflects_pending_count(): void
    {
        Booking::query()->delete();

        $this->assertNull(BookingResource::getNavigationBadge());

        Booking::create([
            'customer_name' => 'Nguyễn Thị Hoa',
            'phone' => '0987654321',
            'status' => 'pending',
        ]);

        Booking::create([
            'customer_name' => 'Trần Thu Hà',
            'phone' => '0912345678',
            'status' => 'pending',
        ]);

        Booking::create([
            'customer_name' => 'Lê Minh Anh',
            'phone' => '0933445566',
            'status' => 'confirmed',
        ]);

        $this->assertEquals('2', BookingResource::getNavigationBadge());
        $this->assertEquals('warning', BookingResource::getNavigationBadgeColor());
    }

    /**
     * Test payment resource badge reflects outstanding debt amount.
     */
    public function test_payment_resource_badge_reflects_outstanding_debt(): void
    {
        Payment::query()->delete();
        Booking::query()->delete();

        $this->assertNull(PaymentResource::getNavigationBadge());

        $booking = Booking::create([
            'customer_name' => 'Lê Phương',
            'phone' => '0977889900',
            'status' => 'confirmed',
            'total_amount' => 2000000,
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'title' => 'Cọc lần 1',
            'amount' => 500000,
            'payment_method' => 'Chuyển khoản',
            'payment_date' => now(),
        ]);

        $this->assertEquals('1.500.000đ', PaymentResource::getNavigationBadge());
        $this->assertEquals('danger', PaymentResource::getNavigationBadgeColor());
    }

    /**
     * Test creating a new booking triggers database notification for admins.
     */
    public function test_creating_booking_triggers_database_notification(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_notify_test@makeup.test'],
            ['name' => 'Admin Notify Test', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $admin->assignRole($this->superAdminRole);

        DB::table('notifications')->where('notifiable_id', $admin->id)->delete();

        Booking::create([
            'customer_name' => 'Vũ Thuỳ Linh',
            'phone' => '0909123456',
            'booking_date' => now()->addDays(3),
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        $notificationCount = DB::table('notifications')
            ->where('notifiable_id', $admin->id)
            ->count();

        $this->assertGreaterThanOrEqual(1, $notificationCount);
    }

    /**
     * Test debt notification on booking creation with total_amount.
     */
    public function test_booking_with_debt_triggers_debt_notification(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_debt_test@makeup.test'],
            ['name' => 'Admin Debt Test', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $admin->assignRole($this->superAdminRole);

        DB::table('notifications')->where('notifiable_id', $admin->id)->delete();

        $booking = Booking::create([
            'customer_name' => 'Phạm Quỳnh Anh',
            'phone' => '0988112233',
            'booking_date' => now()->addDays(5),
            'status' => 'confirmed',
            'total_amount' => 3000000,
        ]);

        $latestNotification = DB::table('notifications')
            ->where('notifiable_id', $admin->id)
            ->orderByDesc('created_at')
            ->first();

        $this->assertNotNull($latestNotification);
        $data = json_decode($latestNotification->data, true);
        $this->assertStringContainsString('Phạm Quỳnh Anh', $data['title'] . $data['body']);
    }

    /**
     * Test login alert job sends database notification.
     */
    public function test_login_alert_job_sends_database_notification(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'login_user_test@makeup.test'],
            ['name' => 'Test Login User', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );
        $user->assignRole($this->superAdminRole);

        DB::table('notifications')->where('notifiable_id', $user->id)->delete();

        $job = new SendLoginAlertJob($user, '127.0.0.1', 'Mozilla/5.0');
        $job->handle();

        $loginNotification = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->first();

        $this->assertNotNull($loginNotification);
        $data = json_decode($loginNotification->data, true);
        $this->assertStringContainsString('Đăng nhập tài khoản', $data['title']);
    }

    /**
     * Test SystemErrorTranslator translates various technical exceptions into friendly Vietnamese.
     */
    public function test_system_error_translator_vietnamese_mapping(): void
    {
        // 1. SMTP / Email error
        $smtpException = new TransportException('Connection could not be established with host mail.example.com:25');
        $translatedSmtp = SystemErrorTranslator::translate($smtpException);
        $this->assertStringContainsString('SMTP', $translatedSmtp['title']);
        $this->assertStringContainsString('MAIL_HOST', $translatedSmtp['body']);

        // 2. Media Optimizer error
        $mediaError = 'MediaOptimizer compression error: FFmpeg binary not found';
        $translatedMedia = SystemErrorTranslator::translate($mediaError);
        $this->assertStringContainsString('Media Optimizer', $translatedMedia['title']);
        $this->assertStringContainsString('tối ưu', $translatedMedia['body']);

        // 3. Database error
        $dbError = 'SQLSTATE[HY000] [2002] Connection refused (Connection: mysql)';
        $translatedDb = SystemErrorTranslator::translate($dbError);
        $this->assertStringContainsStringIgnoringCase('cơ sở dữ liệu', $translatedDb['title']);
        $this->assertStringContainsString('MySQL', $translatedDb['body']);

        // 4. Storage permission error
        $storageError = 'file_put_contents(/storage/app/public/test.jpg): Failed to open stream: Permission denied';
        $translatedStorage = SystemErrorTranslator::translate($storageError);
        $this->assertStringContainsStringIgnoringCase('tập tin', $translatedStorage['title']);
        $this->assertStringContainsString('Storage Permissions', $translatedStorage['body']);
    }

    /**
     * Test admin dashboard page loads with database notifications enabled.
     */
    public function test_admin_dashboard_loads_with_database_notifications(): void
    {
        $admin = User::factory()->create([
            'email' => 'superadmin_' . uniqid() . '@example.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($this->superAdminRole);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    /**
     * Test public frontend home page access does not trigger login alerts.
     */
    public function test_login_alert_is_not_dispatched_on_public_frontend(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $user = User::factory()->create([
            'email' => 'frontend_visitor_' . uniqid() . '@example.com',
            'email_verified_at' => now(),
        ]);

        $listener = new \App\Listeners\SendLoginAlert();
        
        // Giả lập request vào trang chủ công khai "/"
        $request = \Illuminate\Http\Request::create('/', 'GET');
        app()->instance('request', $request);

        $event = new \Illuminate\Auth\Events\Login('web', $user, false);
        $listener->handle($event);

        \Illuminate\Support\Facades\Queue::assertNotPushed(\App\Jobs\SendLoginAlertJob::class);
    }
}

