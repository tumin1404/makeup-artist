<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\Banner;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Portfolio;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->superAdmin = User::factory()->create([
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);
        $this->superAdmin->assignRole($role);
    }

    public function test_admin_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_services_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/services');
        $response->assertStatus(200);
    }

    public function test_admin_bookings_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/bookings');
        $response->assertStatus(200);
    }

    public function test_admin_banners_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/banners');
        $response->assertStatus(200);
    }

    public function test_admin_categories_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/categories');
        $response->assertStatus(200);
    }

    public function test_admin_expenses_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/expenses');
        $response->assertStatus(200);
    }

    public function test_admin_payments_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/payments');
        $response->assertStatus(200);
    }

    public function test_admin_portfolios_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/portfolios');
        $response->assertStatus(200);
    }

    public function test_admin_posts_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/posts');
        $response->assertStatus(200);
    }

    public function test_admin_settings_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/settings');
        $response->assertStatus(200);
    }

    public function test_admin_users_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/users');
        $response->assertStatus(200);
    }
}
