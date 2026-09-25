<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_index_displays_active_services(): void
    {
        $category = Category::create([
            'name' => 'Dịch vụ Cưới',
            'slug' => 'dich-vu-cuoi',
            'type' => Category::TYPE_SERVICE,
        ]);

        $activeService = Service::create([
            'category_id' => $category->id,
            'name' => 'Trang Điểm Cô Dâu Vip',
            'price_text' => '2.000.000 VNĐ',
            'is_active' => true,
        ]);

        $inactiveService = Service::create([
            'category_id' => $category->id,
            'name' => 'Dịch Vụ Tạm Đóng',
            'price_text' => '100.000 VNĐ',
            'is_active' => false,
        ]);

        $response = $this->get(route('services.index'));
        $response->assertStatus(200);
        $response->assertSee('Trang Điểm Cô Dâu Vip');
        $response->assertDontSee('Dịch Vụ Tạm Đóng');
    }

    public function test_service_show_displays_service_detail(): void
    {
        $category = Category::create([
            'name' => 'Dịch vụ Cưới',
            'slug' => 'dich-vu-cuoi',
            'type' => Category::TYPE_SERVICE,
        ]);

        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Makeup Dự Tiệc Sang Trọng',
            'price_text' => '800.000 VNĐ',
            'is_active' => true,
        ]);

        $response = $this->get(route('services.show', $service->id));
        $response->assertStatus(200);
        $response->assertSee('Makeup Dự Tiệc Sang Trọng');
    }

    public function test_service_show_returns_404_for_invalid_id(): void
    {
        $response = $this->get(route('services.show', 99999));
        $response->assertStatus(404);
    }
}
