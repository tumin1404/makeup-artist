<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Booking;
use App\Filament\Resources\BookingResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class LatestBookingsTable extends BaseWidget
{
    protected static ?string $heading = 'Lịch hẹn & Yêu cầu đặt lịch mới nhất';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()->latest('id')
            )
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->description(fn (Booking $record): string => $record->phone ?? '')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('booking_date')
                    ->label('Ngày & Giờ hẹn')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                TextColumn::make('items.service_name')
                    ->label('Dịch vụ / Gói làm đẹp')
                    ->bulleted()
                    ->limitList(2)
                    ->placeholder('Trang điểm theo yêu cầu'),

                TextColumn::make('total_amount')
                    ->label('Tổng hợp đồng')
                    ->money('VND')
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('payment_status')
                    ->label('Thanh toán & Công nợ')
                    ->state(function (Booking $record): string {
                        $paid = $record->payments->sum('amount');
                        $remaining = ($record->total_amount ?? 0) - $paid;

                        if ($record->status === 'canceled') {
                            return 'Đã hủy đơn';
                        }

                        if ($remaining <= 0 && $paid > 0) {
                            return 'Đã thanh toán đủ (' . number_format($paid, 0, ',', '.') . 'đ)';
                        }

                        if ($paid > 0) {
                            return 'Đã cọc: ' . number_format($paid, 0, ',', '.') . 'đ | Còn: ' . number_format($remaining, 0, ',', '.') . 'đ';
                        }

                        return 'Chưa thanh toán (' . number_format($record->total_amount ?? 0, 0, ',', '.') . 'đ)';
                    })
                    ->badge()
                    ->color(function (Booking $record): string {
                        $paid = $record->payments->sum('amount');
                        $remaining = ($record->total_amount ?? 0) - $paid;

                        if ($record->status === 'canceled') {
                            return 'gray';
                        }

                        if ($remaining <= 0 && $paid > 0) {
                            return 'success';
                        }

                        if ($paid > 0) {
                            return 'warning';
                        }

                        return 'danger';
                    }),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'completed' => 'Hoàn thành',
                        'canceled' => 'Đã hủy',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Chi tiết / Xử lý')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    ->url(fn (Booking $record): string => BookingResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
