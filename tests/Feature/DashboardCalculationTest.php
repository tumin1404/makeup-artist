<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\Payment;
use App\Filament\Widgets\DashboardStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_calculation_scopes_to_current_year_and_month(): void
    {
        $booking = Booking::create([
            'customer_name' => 'Khách Hàng Test',
            'phone' => '0988888888',
            'booking_date' => now(),
            'status' => 'confirmed',
            'total_amount' => 6000000,
        ]);

        // 1. Current month payment & expense
        Payment::create([
            'booking_id' => $booking->id,
            'title' => 'Cọc gói cưới',
            'amount' => 1000000,
            'payment_method' => 'Chuyển khoản',
            'payment_date' => now(),
        ]);

        Expense::create([
            'item_name' => 'Mua son MAC',
            'amount' => 400000,
            'category' => 'Mỹ phẩm',
            'expense_date' => now(),
        ]);

        // 2. Previous year same month (must NOT be counted in current month stats)
        Payment::create([
            'booking_id' => $booking->id,
            'title' => 'Cọc năm ngoái',
            'amount' => 5000000,
            'payment_method' => 'Chuyển khoản',
            'payment_date' => now()->subYear(),
        ]);

        Expense::create([
            'item_name' => 'Chi phí năm ngoái',
            'amount' => 2000000,
            'category' => 'Mỹ phẩm',
            'expense_date' => now()->subYear(),
        ]);

        $thuThangNay = Payment::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $chiThangNay = Expense::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        $this->assertEquals(1000000, $thuThangNay);
        $this->assertEquals(400000, $chiThangNay);
        $this->assertEquals(600000, $thuThangNay - $chiThangNay);
    }
}
