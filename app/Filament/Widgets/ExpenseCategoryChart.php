<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Cơ cấu Chi phí theo Danh mục';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '320px';

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'year' => 'Năm ' . now()->year,
            'month' => 'Tháng này (T' . now()->month . ')',
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $query = Expense::query();

        if ($this->filter === 'month') {
            $query->whereYear('expense_date', now()->year)
                  ->whereMonth('expense_date', now()->month);
        } else {
            $query->whereYear('expense_date', now()->year);
        }

        $expenses = $query->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        if ($expenses->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#4b5563'],
                    ],
                ],
                'labels' => ['Chưa có dữ liệu'],
            ];
        }

        $colorMap = [
            'Mặt bằng & Tiện ích' => '#8b5cf6', // Tím Pastel
            'Mỹ phẩm' => '#ec4899',            // Hồng Phấn
            'Dụng cụ' => '#3b82f6',            // Xanh Dương
            'Marketing' => '#f59e0b',          // Vàng Cam
            'Phụ kiện' => '#10b981',           // Xanh Ngọc
            'Vận hành' => '#06b6d4',           // Xanh Cyan
        ];

        $labels = [];
        $data = [];
        $backgroundColors = [];
        $totalAll = $expenses->sum('total');

        foreach ($expenses as $item) {
            $cat = $item->category ?: 'Khác';
            $percent = $totalAll > 0 ? round(($item->total / $totalAll) * 100, 1) : 0;
            $labels[] = "{$cat} ({$percent}%)";
            $data[] = (float) $item->total;
            $backgroundColors[] = $colorMap[$cat] ?? '#94a3b8';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Chi phí (VNĐ)',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 2,
                    'borderColor' => 'transparent',
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '68%',
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'boxWidth' => 8,
                        'padding' => 15,
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => '(context) => " " + context.label + ": " + new Intl.NumberFormat("vi-VN").format(context.raw) + " đ"',
                    ],
                ],
            ],
        ];
    }
}
