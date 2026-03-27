<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Expense;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Đơn chờ xử lý
        $pendingBookings = Booking::where('status', 'pending')->count();

        // 2. Lợi nhuận tháng này
        $thuThangNay = Payment::whereMonth('payment_date', now()->month)->sum('amount');
        $chiThangNay = Expense::whereMonth('expense_date', now()->month)->sum('amount');
        $loiNhuan = $thuThangNay - $chiThangNay;

        // 3. Tính công nợ (Tổng tiền của các booking KHÁC trạng thái 'Hủy' trừ đi tổng tiền đã thu)
        $tongTienHopDong = Booking::where('status', '!=', 'canceled')->sum('total_amount');
        $tongTienDaThu = Payment::sum('amount');
        $congNo = $tongTienHopDong - $tongTienDaThu;
        // Đảm bảo công nợ không bị âm (nếu lỡ nhập sai)
        $congNo = $congNo > 0 ? $congNo : 0;

        return [
            Stat::make('Đơn chờ xử lý', $pendingBookings)
                ->description('Khách mới đặt lịch cần xác nhận')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Lợi nhuận (Tháng này)', number_format($loiNhuan, 0, ',', '.') . 'đ')
                ->description('Thu: ' . number_format($thuThangNay) . 'đ | Chi: ' . number_format($chiThangNay) . 'đ')
                ->descriptionIcon($loiNhuan >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($loiNhuan >= 0 ? 'success' : 'danger'),

            Stat::make('Công nợ cần thu', number_format($congNo, 0, ',', '.') . 'đ')
                ->description('Tổng tiền khách chưa thanh toán hết')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),
        ];
    }
}