<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Dịch vụ / Báo giá';
    protected static ?string $modelLabel = 'Dịch vụ';
    protected static ?string $pluralModelLabel = 'Danh sách Dịch vụ';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin dịch vụ')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên dịch vụ')
                            ->required()
                            ->placeholder('VD: Makeup cô dâu ngày cưới'),
                        TextInput::make('price_text')
                            ->label('Giá hiển thị')
                            ->placeholder('VD: Từ 1.500.000 VNĐ'),
                        Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Toggle::make('is_active')
                            ->label('Hiển thị trên Web')
                            ->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Chi tiết & Quyền lợi')
                    ->schema([
                        Textarea::make('short_description')
                            ->label('Mô tả ngắn')
                            ->columnSpanFull(),
                        TagsInput::make('features')
                            ->label('Các đặc điểm / Quyền lợi')
                            ->placeholder('Thêm đặc điểm (Nhấn Enter)')
                            ->helperText('VD: Tặng dán kích mí, Hỗ trợ làm tóc, Sử dụng mỹ phẩm High-end...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Tên dịch vụ')->searchable()->sortable(),
                TextColumn::make('price_text')->label('Giá'),
                TextColumn::make('category.name')->label('Danh mục'),
                IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean(),
            ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}