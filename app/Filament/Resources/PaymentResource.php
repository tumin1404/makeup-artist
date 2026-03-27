<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\RawJs;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Quản lý Tài chính';
    protected static ?string $modelLabel = 'Giao dịch thu';
    protected static ?string $pluralModelLabel = 'Thanh toán & Công nợ';
    protected static ?int $navigationSort = 2; // Nằm dưới trang Chi phí

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin thu tiền')
                    ->schema([
                        Forms\Components\Select::make('booking_id')
                            ->label('Đơn đặt lịch (Booking)')
                            ->relationship('booking', 'customer_name')
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Booking $record) => "#{$record->id} - {$record->customer_name} - {$record->phone}")
                            ->searchable(['customer_name', 'phone', 'id']) // Cho phép tìm kiếm bằng cả tên, SĐT hoặc ID
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề thu')
                            ->placeholder('VD: Khách cọc giữ lịch, Thanh toán nốt...')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Số tiền thu')
                            ->numeric()
                            ->required()
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')) // Dấu phẩy cho phần thập phân, dấu chấm cho hàng nghìn
                            ->stripCharacters('.'),
                        Forms\Components\Select::make('payment_method')
                            ->label('Hình thức')
                            ->options([
                                'Chuyển khoản' => 'Chuyển khoản',
                                'Tiền mặt' => 'Tiền mặt',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('payment_date')
                            ->label('Ngày giờ thu')
                            ->default(now())
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Minh chứng')
                    ->schema([
                        Forms\Components\FileUpload::make('proof_image')
                            ->label('Ảnh bill chuyển khoản / Biên nhận')
                            ->image()
                            ->directory('payments/proofs'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking.customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền')
                    ->money('VND')
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Hình thức')
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Ngày thu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('payment_date', 'desc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}