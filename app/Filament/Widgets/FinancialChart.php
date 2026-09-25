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
    protected static ?string $heading = 'Xu hướng Thu - Chi & Lợi nhuận';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '320px';

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'year' => 'Cả năm ' . now()->year,
            'q1' => 'Quý 1 (T1 - T3)',
            'q2' => 'Quý 2 (T4 - T6)',
            'q3' => 'Quý 3 (T7 - T9)',
            'q4' => 'Quý 4 (T10 - T12)',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = now()->year;

        // Xác định khoảng thời gian theo bộ lọc
        [$startDate, $endDate] = match ($this->filter) {
            'q1' => [Carbon::create($year, 1, 1)->startOfDay(), Carbon::create($year, 3, 31)->endOfDay()],
            'q2' => [Carbon::create($year, 4, 1)->startOfDay(), Carbon::create($year, 6, 30)->endOfDay()],
            'q3' => [Carbon::create($year, 7, 1)->startOfDay(), Carbon::create($year, 9, 30)->endOfDay()],
            'q4' => [Carbon::create($year, 10, 1)->startOfDay(), Carbon::create($year, 12, 31)->endOfDay()],
            default => [Carbon::create($year, 1, 1)->startOfDay(), Carbon::create($year, 12, 31)->endOfDay()],
        };

        // 1. Lấy dữ liệu Doanh thu (Payments)
        $thuData = Trend::model(Payment::class)
            ->between(start: $startDate, end: $endDate)
            ->dateColumn('payment_date')
            ->perMonth()
            ->sum('amount');

        // 2. Lấy dữ liệu Chi phí (Expenses)
        $chiData = Trend::model(Expense::class)
            ->between(start: $startDate, end: $endDate)
            ->dateColumn('expense_date')
            ->perMonth()
            ->sum('amount');

        $thuValues = $thuData->map(fn (TrendValue $value) => (float) $value->aggregate)->toArray();
        $chiValues = $chiData->map(fn (TrendValue $value) => (float) $value->aggregate)->toArray();

        // 3. Tính Lợi nhuận ròng (Thu - Chi)
        $loiNhuanValues = [];
        foreach ($thuValues as $index => $thu) {
            $chi = $chiValues[$index] ?? 0;
            $loiNhuanValues[] = $thu - $chi;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu (VNĐ)',
                    'data' => $thuValues,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#ffffff',
                    'pointHoverRadius' => 6,
                    'pointRadius' => 4,
                    'borderWidth' => 2.5,
                ],
                [
                    'label' => 'Chi phí (VNĐ)',
                    'data' => $chiValues,
                    'borderColor' => '#f43f5e',
                    'backgroundColor' => 'rgba(244, 63, 94, 0.08)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#f43f5e',
                    'pointBorderColor' => '#ffffff',
                    'pointHoverRadius' => 6,
                    'pointRadius' => 4,
                    'borderWidth' => 2.5,
                ],
                [
                    'label' => 'Lợi nhuận ròng (VNĐ)',
                    'data' => $loiNhuanValues,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'transparent',
                    'borderDash' => [5, 5],
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#f59e0b',
                    'pointBorderColor' => '#ffffff',
                    'pointHoverRadius' => 6,
                    'pointRadius' => 4,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $thuData->map(fn (TrendValue $value) => 'Tháng ' . Carbon::parse($value->date)->format('n')),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'boxWidth' => 8,
                        'font' => [
                            'family' => 'inherit',
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'grid' => [
                        'color' => 'rgba(156, 163, 175, 0.1)',
                    ],
                    'ticks' => [
                        'callback' => '(val) => (val >= 1000000 ? (val / 1000000) + " Tr" : val)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}