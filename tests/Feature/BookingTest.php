<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_page_renders_successfully(): void
    {
        $category = Category::create([
            'name' => 'Dịch vụ Cưới',
            'slug' => 'dich-vu-cuoi',
            'type' => Category::TYPE_SERVICE,
        ]);

        Service::create([
            'category_id' => $category->id,
            'name' => 'Makeup Cô Dâu',
            'price_text' => '1.500.000 VNĐ',
            'is_active' => true,
        ]);

        $response = $this->get(route('booking.index'));
        $response->assertStatus(200);
        $response->assertSee('Makeup Cô Dâu');
    }

    public function test_booking_can_be_submitted_with_valid_data(): void
    {
        $category = Category::create([
            'name' => 'Dịch vụ Cưới',
            'slug' => 'dich-vu-cuoi',
            'type' => Category::TYPE_SERVICE,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Makeup Cô Dâu',
            'price_text' => '1.500.000 VNĐ',
            'is_active' => true,
        ]);

        $postData = [
            'customer_name' => 'Nguyễn Thị Hoa',
            'phone' => '0987654321',
            'social_link' => 'https://facebook.com/hoanguyen',
            'booking_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'service_ids' => [$service->id],
            'message' => 'Yêu cầu trang điểm nhẹ nhàng tự nhiên.',
        ];

        $response = $this->post(route('booking.store'), $postData);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Nguyễn Thị Hoa',
            'phone' => '0987654321',
            'status' => 'pending',
        ]);
    }

    public function test_booking_fails_validation_when_required_fields_are_missing(): void
    {
        $response = $this->post(route('booking.store'), []);
        $response->assertSessionHasErrors(['customer_name', 'phone', 'booking_date', 'service_ids']);
    }

    public function test_booking_fails_when_service_does_not_exist(): void
    {
        $postData = [
            'customer_name' => 'Nguyễn Thị Hoa',
            'phone' => '0987654321',
            'booking_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'service_ids' => [9999],
        ];

        $response = $this->post(route('booking.store'), $postData);
        $response->assertSessionHasErrors(['service_ids.0']);
    }

    public function test_booking_can_be_submitted_via_ajax_and_returns_json(): void
    {
        $category = Category::create([
            'name' => 'Dịch vụ Cưới',
            'slug' => 'dich-vu-cuoi-ajax',
            'type' => Category::TYPE_SERVICE,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Makeup Cô Dâu Vip',
            'price_text' => '2.500.000 VNĐ',
            'is_active' => true,
        ]);

        $postData = [
            'customer_name' => 'Lê Phương Thảo',
            'phone' => '0912345678',
            'booking_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'service_ids' => [$service->id],
            'message' => 'Tư vấn layout sang trọng',
        ];

        $response = $this->postJson(route('booking.store'), $postData);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'customer_name' => 'Lê Phương Thảo',
                'phone' => '0912345678',
            ],
        ]);

        $this->assertDatabaseHas('bookings', [
            'customer_name' => 'Lê Phương Thảo',
            'phone' => '0912345678',
            'status' => 'pending',
        ]);
    }

    public function test_booking_ajax_fails_validation_and_returns_json_errors(): void
    {
        $response = $this->postJson(route('booking.store'), []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_name', 'phone', 'booking_date', 'service_ids']);
    }
}
