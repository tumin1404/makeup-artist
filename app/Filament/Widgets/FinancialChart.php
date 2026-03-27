<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Payment;
use App\Models\Expense;
use Carbon\Carbon;

class FinancialChart extends ChartWidget
{
    protected static ?string $heading = 'Biểu đồ Thu / Chi năm nay';
    protected static ?int $sort = 2; // Xếp dưới các thẻ thống kê
    
    // Mặc định biểu đồ là dạng Cột
    protected function getType(): string
    {
        return 'bar'; 
    }

    protected function getData(): array
    {
        // 1. Lấy dữ liệu Doanh thu (Payments) theo từng tháng trong năm nay
        $thuData = Trend::model(Payment::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->dateColumn('payment_date')
            ->perMonth()
            ->sum('amount');

        // 2. Lấy dữ liệu Chi phí (Expenses) theo từng tháng trong năm nay
        $chiData = Trend::model(Expense::class)
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->dateColumn('expense_date')
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu (VNĐ)',
                    'data' => $thuData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#10b981', // Màu xanh lá
                ],
                [
                    'label' => 'Chi phí (VNĐ)',
                    'data' => $chiData->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#ef4444', // Màu đỏ
                ],
            ],
            // Hiển thị nhãn là các tháng (Tháng 1, Tháng 2...)
            'labels' => $thuData->map(fn (TrendValue $value) => 'Tháng ' . Carbon::parse($value->date)->format('n')),
        ];
    }
}