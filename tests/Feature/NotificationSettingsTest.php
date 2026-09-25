<?php

namespace Tests\Feature;

use App\Filament\Pages\NotificationSettings;
use App\Jobs\SendLoginAlertJob;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Services\MailSettingService;
use App\Services\NotificationDispatcherService;
use App\Services\TelegramNotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationSettingsTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('site_settings');
        Mail::fake();
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true, 'result' => ['message_id' => 101]], 200),
            'http://ip-api.com/*' => Http::response(['status' => 'success', 'city' => 'Hanoi', 'country' => 'Vietnam'], 200),
        ]);

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->admin = User::factory()->create([
            'email' => 'admin_notif_' . uniqid() . '@example.com',
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole($role);
    }

    /**
     * Test admin can access Notification Settings page in Filament Admin.
     */
    public function test_admin_can_access_notification_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/notification-settings');
        $response->assertStatus(200);
        $response->assertSee('Cấu hình Email, Telegram &amp; Zalo Thông Báo', false);
        $response->assertSee('Máy Chủ Email', false);
        $response->assertSee('Thông Báo Telegram', false);
    }

    /**
     * Test saving notification settings persists data to settings table.
     */
    public function test_saving_notification_settings_persists_to_database(): void
    {
        Livewire::actingAs($this->admin)
            ->test(NotificationSettings::class)
            ->fillForm([
                'mail_mailer' => 'smtp',
                'mail_host' => 'mail.linhmakeup.vn',
                'mail_port' => '465',
                'mail_encryption' => 'ssl',
                'mail_username' => 'booking@linhmakeup.vn',
                'mail_password' => 'secretpassword123',
                'mail_from_address' => 'booking@linhmakeup.vn',
                'mail_from_name' => 'Linh Makeup Studio',
                'mail_notify_booking_enabled' => true,
                'mail_notify_booking_subject' => '[{site_name}] Thư đặt lịch mới: {customer_name}',
                'telegram_notify_enabled' => true,
                'telegram_bot_token' => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',
                'telegram_chat_id' => '987654321',
                'telegram_notify_booking_enabled' => true,
                'telegram_template_booking' => 'Telegram test booking {customer_name}',
                'zalo_notify_enabled' => true,
                'zalo_oa_id' => 'oa_12345',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('mail.linhmakeup.vn', Setting::get('mail_host'));
        $this->assertEquals('465', Setting::get('mail_port'));
        $this->assertEquals('1', Setting::get('telegram_notify_enabled'));
        $this->assertEquals('123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11', Setting::get('telegram_bot_token'));
        $this->assertEquals('987654321', Setting::get('telegram_chat_id'));
        $this->assertEquals('1', Setting::get('zalo_notify_enabled'));
        $this->assertEquals('Telegram test booking {customer_name}', Setting::get('telegram_template_booking'));
    }

    /**
     * Test dynamic mail configuration loader updates Laravel runtime config.
     */
    public function test_mail_setting_service_applies_smtp_config_dynamically(): void
    {
        Setting::updateOrCreate(['key' => 'mail_mailer'], ['group' => 'mail', 'value' => 'smtp', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_host'], ['group' => 'mail', 'value' => 'smtp.customdomain.com', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_port'], ['group' => 'mail', 'value' => '465', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_encryption'], ['group' => 'mail', 'value' => 'ssl', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_username'], ['group' => 'mail', 'value' => 'info@customdomain.com', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_password'], ['group' => 'mail', 'value' => 'MyP@ssw0rd', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_from_address'], ['group' => 'mail', 'value' => 'no-reply@customdomain.com', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_from_name'], ['group' => 'mail', 'value' => 'Custom Brand Studio', 'type' => 'text']);

        Cache::forget('site_settings');
        MailSettingService::applyConfig();

        $this->assertEquals('smtp', Config::get('mail.default'));
        $this->assertEquals('smtp.customdomain.com', Config::get('mail.mailers.smtp.host'));
        $this->assertEquals(465, Config::get('mail.mailers.smtp.port'));
        $this->assertEquals('ssl', Config::get('mail.mailers.smtp.encryption'));
        $this->assertEquals('info@customdomain.com', Config::get('mail.mailers.smtp.username'));
        $this->assertEquals('MyP@ssw0rd', Config::get('mail.mailers.smtp.password'));
        $this->assertEquals('no-reply@customdomain.com', Config::get('mail.from.address'));
        $this->assertEquals('Custom Brand Studio', Config::get('mail.from.name'));
    }

    /**
     * Test booking creation triggers Telegram notification when enabled.
     */
    public function test_booking_creation_triggers_telegram_notification(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true, 'result' => ['message_id' => 101]], 200),
        ]);

        Setting::updateOrCreate(['key' => 'telegram_notify_enabled'], ['group' => 'telegram', 'value' => '1', 'type' => 'toggle']);
        Setting::updateOrCreate(['key' => 'telegram_notify_booking_enabled'], ['group' => 'telegram', 'value' => '1', 'type' => 'toggle']);
        Setting::updateOrCreate(['key' => 'telegram_bot_token'], ['group' => 'telegram', 'value' => '123456:FAKE_TOKEN_ABC', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['group' => 'telegram', 'value' => '1122334455', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'site_name'], ['group' => 'general', 'value' => 'Linh Beauty Studio', 'type' => 'text']);

        Setting::updateOrCreate(['key' => 'telegram_template_booking'], ['group' => 'telegram', 'value' => '✨ [{site_name}] Đơn mới: {customer_name} - SĐT: {phone}', 'type' => 'textarea']);

        Cache::forget('site_settings');

        $service = Service::firstOrCreate(
            ['name' => 'Trang Điểm Cô Dâu VIP'],
            ['price_text' => '2.000.000đ', 'is_active' => true]
        );

        $booking = Booking::create([
            'customer_name' => 'Nguyễn Thị Ngọc Ánh',
            'phone' => '0987654321',
            'status' => 'pending',
            'total_amount' => 2000000,
            'service_ids' => [$service->id],
            'booking_date' => now()->addDays(3)->format('Y-m-d 08:30:00'),
            'notes' => 'Trang điểm tại nhà riêng',
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '123456:FAKE_TOKEN_ABC')
                && $request['chat_id'] === '1122334455'
                && str_contains($request['text'], 'Nguyễn Thị Ngọc Ánh')
                && str_contains($request['text'], '0987654321')
                && str_contains($request['text'], 'Linh Beauty Studio');
        });
    }

    /**
     * Test booking creation does not trigger Telegram when disabled.
     */
    public function test_booking_creation_does_not_trigger_telegram_when_disabled(): void
    {
        Http::fake();

        Setting::updateOrCreate(['key' => 'telegram_notify_enabled'], ['group' => 'telegram', 'value' => '0', 'type' => 'toggle']);
        Setting::updateOrCreate(['key' => 'telegram_bot_token'], ['group' => 'telegram', 'value' => '123456:FAKE_TOKEN', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['group' => 'telegram', 'value' => '1122334455', 'type' => 'text']);

        Cache::forget('site_settings');

        Booking::create([
            'customer_name' => 'Khách Hàng Test',
            'phone' => '0911223344',
            'status' => 'pending',
        ]);

        Http::assertNothingSent();
    }

    /**
     * Test login alert dispatches Telegram notification when enabled.
     */
    public function test_login_alert_dispatches_telegram_notification(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
            'http://ip-api.com/*' => Http::response(['status' => 'success', 'city' => 'Hanoi', 'country' => 'Vietnam'], 200),
        ]);

        Setting::updateOrCreate(['key' => 'telegram_notify_enabled'], ['group' => 'telegram', 'value' => '1', 'type' => 'toggle']);
        Setting::updateOrCreate(['key' => 'telegram_notify_login_enabled'], ['group' => 'telegram', 'value' => '1', 'type' => 'toggle']);
        Setting::updateOrCreate(['key' => 'telegram_bot_token'], ['group' => 'telegram', 'value' => '123456:FAKE_TOKEN', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['group' => 'telegram', 'value' => '998877', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'mail_notify_login_enabled'], ['group' => 'mail', 'value' => '0', 'type' => 'toggle']);

        Cache::forget('site_settings');

        $job = new SendLoginAlertJob($this->admin, '1.2.3.4', 'Chrome MacOS');
        $job->handle();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && $request['chat_id'] === '998877'
                && str_contains($request['text'], 'CẢNH BÁO ĐĂNG NHẬP')
                && str_contains($request['text'], '1.2.3.4');
        });
    }

    /**
     * Test booking status update dispatches status change notification.
     */
    public function test_booking_status_update_dispatches_notification(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        Setting::updateOrCreate(['key' => 'telegram_notify_enabled'], ['group' => 'telegram', 'value' => '1', 'type' => 'toggle']);
        Setting::updateOrCreate(['key' => 'telegram_notify_status_enabled'], ['group' => 'telegram', 'value' => '1', 'type' => 'toggle']);
        Setting::updateOrCreate(['key' => 'telegram_bot_token'], ['group' => 'telegram', 'value' => '123456:STATUS_TOKEN', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'telegram_chat_id'], ['group' => 'telegram', 'value' => '554433', 'type' => 'text']);

        Cache::forget('site_settings');

        $booking = Booking::create([
            'customer_name' => 'Trần Thu Trang',
            'phone' => '0909123456',
            'status' => 'pending',
        ]);

        $booking->update(['status' => 'confirmed']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && $request['chat_id'] === '554433'
                && str_contains($request['text'], 'CẬP NHẬT TRẠNG THÁI LỊCH HẸN')
                && str_contains($request['text'], 'Đã xác nhận');
        });
    }

    /**
     * Test sending test Telegram message via TelegramNotificationService.
     */
    public function test_telegram_test_notification_service(): void
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        $result = TelegramNotificationService::sendTestNotification('TOKEN_123', 'CHAT_456');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('thành công', $result['message']);

        Http::assertSent(function ($request) {
            return $request['chat_id'] === 'CHAT_456'
                && str_contains($request['text'], 'KIỂM TRA KẾT NỐI TELEGRAM BOT');
        });
    }

    /**
     * Test sending test email via MailSettingService.
     */
    public function test_mail_test_sending_service(): void
    {
        Mail::fake();

        $result = MailSettingService::sendTestMail('test_recipient@example.com');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('test_recipient@example.com', $result['message']);
    }

    /**
     * Test artisan admin:reset-password command updates user password.
     */
    public function test_admin_reset_password_artisan_command(): void
    {
        $this->artisan('admin:reset-password', [
            'email' => $this->admin->email,
            '--password' => 'newSecretPass2026!',
        ])
        ->expectsOutputToContain('Đặt lại mật khẩu thành công')
        ->assertExitCode(0);

        $this->admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newSecretPass2026!', $this->admin->password));
    }
}
