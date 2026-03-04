<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;   
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Đặt lịch / Booking';
    protected static ?string $modelLabel = 'Đơn Đặt Lịch';
    // Tên hiển thị số nhiều (Sửa lỗi "Lịches" ở tiêu đề trang danh sách)
    protected static ?string $pluralModelLabel = 'Danh Sách Đặt Lịch';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin khách hàng')
                    ->description('Dữ liệu liên hệ cá nhân')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Tên khách hàng')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('zalo')
                            ->label('Số Zalo')
                            ->maxLength(255),
                        TextInput::make('social_link')
                            ->label('Link Facebook/IG')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Chi tiết dịch vụ')
                    ->schema([
                        Select::make('service_id')
                            ->relationship('service', 'name')
                            ->label('Dịch vụ quan tâm')
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('booking_date')
                            ->label('Ngày & Giờ hẹn')
                            ->native(false),
                        Textarea::make('message')
                            ->label('Lời nhắn / Yêu cầu')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Trạng thái & Ghi chú (Nội bộ)')
                    ->schema([
                        Select::make('status')
                            ->label('Trạng thái xử lý')
                            ->options([
                                'pending' => 'Chờ xử lý (Mới)',
                                'confirmed' => 'Đã xác nhận (Chốt lịch)',
                                'completed' => 'Đã hoàn thành',
                                'canceled' => 'Đã hủy',
                            ])
                            ->required()
                            ->default('pending')
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Ghi chú của Admin (Khách không thấy)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone')
                    ->label('SĐT')
                    ->searchable()
                    ->copyable(), // Click vào là copy luôn SĐT
                TextColumn::make('service.name')
                    ->label('Dịch vụ')
                    ->sortable(),
                TextColumn::make('booking_date')
                    ->label('Thời gian hẹn')
                    ->dateTime('d/m/Y H:i') // Chữ H:i sẽ hiển thị thêm giờ:phút
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge() // Biến thành dạng Label màu sắc
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',   // Màu vàng
                        'confirmed' => 'info',    // Màu xanh dương
                        'completed' => 'success', // Màu xanh lá
                        'canceled' => 'danger',   // Màu đỏ
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Chờ xử lý',
                        'confirmed' => 'Đã chốt',
                        'completed' => 'Hoàn thành',
                        'canceled' => 'Đã hủy',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('booking_date', 'asc') 
            
            ->filters([
                // 2. THÊM ĐOẠN NÀY: Tạo bộ lọc nhanh theo Trạng thái
                SelectFilter::make('status')
                    ->label('Lọc trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý (Mới)',
                        'confirmed' => 'Đã xác nhận',
                        'completed' => 'Đã hoàn thành',
                        'canceled' => 'Đã hủy',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}