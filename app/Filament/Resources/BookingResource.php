<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Service;
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
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Str;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Quản lý Website';
    protected static ?string $navigationLabel = 'Đặt lịch';
    protected static ?string $modelLabel = 'Đặt lịch';
    protected static ?string $pluralModelLabel = 'Quản lý đặt lịch';
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

                Section::make('Chi tiết dịch vụ yêu cầu')
                    ->schema([
                        Select::make('service_ids')
                            ->label('Dịch vụ khách quan tâm')
                            ->multiple()
                            ->options(Service::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                            
                        DateTimePicker::make('booking_date')
                            ->label('Ngày & Giờ hẹn dự kiến')
                            ->native(false),
                            
                        Textarea::make('message')
                            ->label('Lời nhắn / Yêu cầu từ khách')
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
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Tổng tiền hóa đơn (VNĐ)')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),
                    
                Section::make('Lịch trình Chi tiết (Sau khi tư vấn)')
                    ->description('Thêm chi tiết lịch trình các ngày và chi phí thực tế vào đây để in hóa đơn.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->hiddenLabel()
                            ->addActionLabel('Thêm lịch trình / dịch vụ')
                            ->schema([
                                Select::make('service_name')
                                    ->label('Chọn dịch vụ')
                                    ->options(Service::pluck('name', 'name'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $service = Service::where('name', $state)->first();
                                        if ($service) {
                                            $priceNumber = preg_replace('/[^0-9]/', '', $service->price_text);
                                            $set('price', $priceNumber ?: 0); 
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(2),
                                DateTimePicker::make('service_date')->label('Ngày giờ thực hiện')->required()->columnSpan(2),
                                TextInput::make('price')->label('Đơn giá')->numeric()->required()->columnSpan(1),
                                TextInput::make('quantity')->label('Số lượng')->numeric()->default(1)->columnSpan(1),
                                TextInput::make('description')->label('Ghi chú thêm')->columnSpan(2),
                            ])
                            ->columns(4)
                    ])
                    ->columnSpanFull(),
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
                    ->copyable(),
                
                \Filament\Tables\Columns\TextColumn::make('service_ids')
                    ->label('Dịch vụ yêu cầu')
                    ->default('Chưa chọn')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) return null;

                        $cleanString = str_replace(['\\', '"', '[', ']'], '', is_array($state) ? implode(',', $state) : $state);
                        $ids = array_filter(array_map('trim', explode(',', $cleanString)));

                        if (empty($ids)) return null;

                        // Chỉ truy vấn DB 1 lần duy nhất
                        static $allServices = null;
                        if ($allServices === null) {
                            $allServices = \App\Models\Service::pluck('name', 'id')->toArray(); 
                        }

                        $names = [];
                        foreach ($ids as $id) {
                            if (isset($allServices[$id])) {
                                $names[] = $allServices[$id];
                            }
                        }
                        
                        // Trả về 1 chuỗi cách nhau bằng dấu phẩy
                        return empty($names) ? null : implode(',', $names); 
                    })
                    ->badge() 
                    ->color('info')
                    ->separator(',') // Filament sẽ tự động đọc dấu phẩy và tách thành nhiều thẻ màu
                    ->wrap(),
                // --------------------------------------------------
                    
                TextColumn::make('booking_date')
                    ->label('Dự kiến')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Lọc trạng thái')
                    ->options([
                        'pending' => 'Chờ xử lý',
                        'confirmed' => 'Đã xác nhận',
                        'completed' => 'Đã hoàn thành',
                        'canceled' => 'Đã hủy',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('print')
                    ->label('In Hoá Đơn')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('booking.invoice', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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