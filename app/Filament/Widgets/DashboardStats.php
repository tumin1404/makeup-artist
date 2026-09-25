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

        // 2. Doanh thu & Chi phí tháng này
        $thuThangNay = Payment::whereYear('payment_date', now()->year)->whereMonth('payment_date', now()->month)->sum('amount');
        $chiThangNay = Expense::whereYear('expense_date', now()->year)->whereMonth('expense_date', now()->month)->sum('amount');
        $loiNhuan = $thuThangNay - $chiThangNay;

        // 3. Tính công nợ (Tổng tiền của các booking KHÁC trạng thái 'Hủy' trừ đi tổng tiền đã thu)
        $tongTienHopDong = Booking::where('status', '!=', 'canceled')->sum('total_amount');
        $tongTienDaThu = Payment::sum('amount');
        $congNo = $tongTienHopDong - $tongTienDaThu;
        $congNo = $congNo > 0 ? $congNo : 0;

        // 4. Tổng doanh thu năm nay
        $tongDoanhThuNam = Payment::whereYear('payment_date', now()->year)->sum('amount');

        return [
            Stat::make('Đơn chờ xử lý', $pendingBookings)
                ->description($pendingBookings > 0 ? "Có {$pendingBookings} khách mới cần xác nhận" : 'Tất cả đơn đã được xử lý')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([1, 0, 2, 1, 0, 1, 0, 1, $pendingBookings])
                ->color($pendingBookings > 0 ? 'warning' : 'success'),

            Stat::make('Doanh thu (Tháng ' . now()->month . ')', number_format($thuThangNay, 0, ',', '.') . 'đ')
                ->description('Tổng tiền thực thu trong tháng')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([21.8, 16.4, 28.6, 22.4, 26.2, 18.3, 23.4, 31.5, $thuThangNay / 1000000])
                ->color('success'),

            Stat::make('Lợi nhuận (Tháng ' . now()->month . ')', number_format($loiNhuan, 0, ',', '.') . 'đ')
                ->description('Thu: ' . number_format($thuThangNay / 1000000, 1) . 'M | Chi: ' . number_format($chiThangNay / 1000000, 1) . 'M')
                ->descriptionIcon($loiNhuan >= 0 ? 'heroicon-m-banknotes' : 'heroicon-m-arrow-trending-down')
                ->chart([9.0, 6.9, 13.4, 10.8, 12.3, 7.1, 10.9, 14.7, $loiNhuan / 1000000])
                ->color($loiNhuan >= 0 ? 'success' : 'danger'),

            Stat::make('Công nợ cần thu', number_format($congNo, 0, ',', '.') . 'đ')
                ->description('Tổng tiền khách chưa thanh toán hết')
                ->descriptionIcon('heroicon-m-receipt-refund')
                ->chart([5.0, 4.0, 8.0, 6.0, 9.0, 7.0, 12.0, 15.0, $congNo / 1000000])
                ->color('danger'),
        ];
    }
}