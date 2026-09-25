<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Service;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommercialSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('site_settings');
    }

    /**
     * Test dynamic site name and hotline update reflects on frontend.
     */
    public function test_changing_site_settings_reflects_on_frontend(): void
    {
        Setting::updateOrCreate(
            ['key' => 'site_name'],
            ['group' => 'general', 'description' => 'Tên web', 'value' => 'Commercial Studio Luxury', 'type' => 'text']
        );

        Setting::updateOrCreate(
            ['key' => 'hotline'],
            ['group' => 'contact', 'description' => 'Hotline', 'value' => '0999.888.777', 'type' => 'text']
        );

        Cache::forget('site_settings');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Commercial Studio Luxury');
        $response->assertSee('0999.888.777');
    }

    /**
     * Test booking page renders dynamic titles and placeholders without hardcoded names.
     */
    public function test_booking_page_renders_dynamic_settings(): void
    {
        Setting::updateOrCreate(
            ['key' => 'booking_title'],
            ['group' => 'booking', 'description' => 'Tiêu đề đặt lịch', 'value' => 'Đặt Lịch Dịch Vụ Cao Cấp', 'type' => 'text']
        );

        Setting::updateOrCreate(
            ['key' => 'address_main'],
            ['group' => 'contact', 'description' => 'Trụ sở chính', 'value' => '123 Đường Luxury, TP. Hồ Chí Minh', 'type' => 'text']
        );

        Cache::forget('site_settings');

        $response = $this->get('/booking');
        $response->assertStatus(200);
        $response->assertSee('Đặt Lịch Dịch Vụ Cao Cấp');
        $response->assertSee('123 Đường Luxury, TP. Hồ Chí Minh');
    }

    /**
     * Test booking submission returns dynamic success message.
     */
    public function test_booking_submission_uses_dynamic_success_message(): void
    {
        $service = Service::firstOrCreate(
            ['name' => 'Makeup Test Service'],
            ['price_text' => '500.000 VNĐ', 'is_active' => true]
        );

        Setting::updateOrCreate(
            ['key' => 'booking_success_message'],
            ['group' => 'booking', 'description' => 'Thông báo', 'value' => 'Yêu cầu của bạn đã được ghi nhận tự động!', 'type' => 'text']
        );

        Cache::forget('site_settings');

        $response = $this->post('/booking', [
            'customer_name' => 'Khách Hàng Mới',
            'phone' => '0901234567',
            'service_ids' => [$service->id],
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertSessionHas('success', 'Yêu cầu của bạn đã được ghi nhận tự động!');
    }

    /**
     * Test invoice displays dynamic bank payment details and customized footer note.
     */
    public function test_invoice_displays_dynamic_banking_and_footer(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create([
            'email' => 'admin_invoice_' . uniqid() . '@example.com',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($role);

        $booking = Booking::create([
            'customer_name' => 'Khách Hàng Hóa Đơn',
            'phone' => '0911223344',
            'status' => 'confirmed',
            'total_amount' => 1500000,
        ]);

        Setting::updateOrCreate(
            ['key' => 'bank_name'],
            ['group' => 'banking', 'description' => 'Ngân hàng', 'value' => 'Techcombank', 'type' => 'text']
        );

        Setting::updateOrCreate(
            ['key' => 'bank_account_number'],
            ['group' => 'banking', 'description' => 'Số TK', 'value' => '190333888999', 'type' => 'text']
        );

        Setting::updateOrCreate(
            ['key' => 'invoice_footer_note'],
            ['group' => 'banking', 'description' => 'Ghi chú', 'value' => 'Hân hạnh được phục vụ quý khách lần tới!', 'type' => 'text']
        );

        Cache::forget('site_settings');

        $response = $this->actingAs($admin)->get("/booking/{$booking->id}/invoice");
        $response->assertStatus(200);
        $response->assertSee('Techcombank');
        $response->assertSee('190333888999');
        $response->assertSee('Hân hạnh được phục vụ quý khách lần tới!');
    }

    /**
     * Test home page renders 2 9:16 portrait cards side-by-side without slider.
     */
    public function test_home_page_renders_side_by_side_before_after_section(): void
    {
        Setting::updateOrCreate(
            ['key' => 'home_transform_title'],
            ['group' => 'home', 'description' => 'Tiêu đề biến hóa', 'value' => 'Nghệ Thuật Biến Hóa Kỳ Diệu', 'type' => 'text']
        );

        Cache::forget('site_settings');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Nghệ Thuật Biến Hóa Kỳ Diệu');
        $response->assertSee('Before');
        $response->assertSee('After');
        $response->assertSee('aspect-[9/16]');
        $response->assertDontSee('img-comparison-slider');
    }
}

