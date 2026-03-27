<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\RawJs;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Quản lý Tài chính';
    protected static ?string $modelLabel = 'Khoản chi';
    protected static ?string $pluralModelLabel = 'Chi phí & Mua sắm';
    protected static ?int $navigationSort = 1; // Sắp xếp trong nhóm

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin khoản chi')
                    ->schema([
                        Forms\Components\TextInput::make('item_name')
                            ->label('Tên sản phẩm / Khoản chi')
                            ->required(),
                        Forms\Components\TextInput::make('category')
                            ->label('Phân loại')
                            ->datalist(function () {
                                // 1. Danh sách gợi ý cực kỳ đầy đủ cho Makeup Artist
                                $defaultCategories = [
                                    'Mỹ phẩm (Son, nền, phấn...)',
                                    'Dụng cụ (Cọ, mút, cốp, máy làm tóc...)',
                                    'Phụ kiện tóc & Trang sức',
                                    'Trang phục (Váy cưới, vest, áo dài...)',
                                    'Di chuyển (Taxi, xe khách, xăng...)',
                                    'Marketing (Chạy Ads, chụp mẫu...)',
                                    'Mặt bằng (Thuê Studio, điện nước...)',
                                    'Nhân sự (Trả lương CTV, thợ phụ...)',
                                    'Ăn uống / Tiếp khách',
                                    'Khác'
                                ];

                                // 2. Lấy thêm các phân loại bạn đã tự nhập vào DB trước đó (tự động học)
                                $existingCategories = \App\Models\Expense::whereNotNull('category')
                                    ->pluck('category')
                                    ->toArray();

                                // 3. Gộp lại và loại bỏ trùng lặp
                                return array_unique(array_merge($defaultCategories, $existingCategories));
                            })
                            ->placeholder('Click đúp để chọn hoặc tự nhập mới...')
                            ->autocomplete(false) // Tắt gợi ý mặc định của trình duyệt web
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Số tiền')
                            ->numeric()
                            ->required()
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)')) // Dấu phẩy cho phần thập phân, dấu chấm cho hàng nghìn
                            ->stripCharacters('.'),
                        Forms\Components\DatePicker::make('expense_date')
                            ->label('Ngày mua/chi')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('buy_link')
                            ->label('Link mua hàng (Shopee/FB...)')
                            ->url()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Hình ảnh & Lưu trữ')
                    ->schema([
                        Forms\Components\FileUpload::make('product_image')
                            ->label('Ảnh vỏ/mẫu sản phẩm (để mua lại)')
                            ->image()
                            ->directory('expenses/products'),
                        Forms\Components\FileUpload::make('receipt_image')
                            ->label('Ảnh hóa đơn/Bill chuyển khoản')
                            ->image()
                            ->directory('expenses/receipts'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product_image')
                    ->label('Sản phẩm')
                    ->square(),
                Tables\Columns\TextColumn::make('item_name')
                    ->label('Tên khoản chi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Phân loại')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Số tiền')
                    ->money('VND')
                    ->sortable()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Ngày chi')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('expense_date', 'desc')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}