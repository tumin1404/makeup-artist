<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InvoiceSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_invoice(): void
    {
        $booking = Booking::create([
            'customer_name' => 'Khách Hàng Bí Mật',
            'phone' => '0912345678',
            'booking_date' => now()->addDays(3),
            'status' => 'confirmed',
            'total_amount' => 2000000,
        ]);

        $response = $this->get(route('booking.invoice', $booking));
        // Must either redirect to login or return 401/403
        $this->assertTrue(in_array($response->status(), [302, 401, 403]));
    }

    public function test_user_without_permission_cannot_access_invoice(): void
    {
        $user = User::factory()->create([
            'email' => 'regular_user@example.com',
        ]);

        $booking = Booking::create([
            'customer_name' => 'Khách Hàng Bí Mật',
            'phone' => '0912345678',
            'booking_date' => now()->addDays(3),
            'status' => 'confirmed',
            'total_amount' => 2000000,
        ]);

        $response = $this->actingAs($user)->get(route('booking.invoice', $booking));
        $response->assertStatus(403);
    }

    public function test_user_with_view_booking_permission_can_access_invoice(): void
    {
        Permission::create(['name' => 'view_booking', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'email' => 'admin_user@example.com',
        ]);
        $user->givePermissionTo('view_booking');

        $booking = Booking::create([
            'customer_name' => 'Khách Hàng Vip',
            'phone' => '0912345678',
            'booking_date' => now()->addDays(3),
            'status' => 'confirmed',
            'total_amount' => 2000000,
        ]);

        $response = $this->actingAs($user)->get(route('booking.invoice', $booking));
        $response->assertStatus(200);
        $response->assertSee('Khách Hàng Vip');
    }
}
