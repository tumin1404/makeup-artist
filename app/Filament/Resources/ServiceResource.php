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
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Quản lý Website';
    protected static ?string $navigationLabel = 'Dịch vụ / Bảng giá';
    protected static ?string $modelLabel = 'Dịch vụ';
    protected static ?string $pluralModelLabel = 'Quản lý Dịch vụ';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin dịch vụ')
                    ->description('Cấu hình tên, giá và phân loại dịch vụ')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên dịch vụ')
                            ->required()
                            ->placeholder('VD: Makeup kỷ yếu - tốt nghiệp'),

                        Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship(
                                name: 'category', 
                                titleAttribute: 'name', 
                                modifyQueryUsing: fn (Builder $query) => $query->where('type', Category::TYPE_SERVICE)
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('price_text')
                            ->label('Giá hiển thị')
                            ->placeholder('VD: 400k hoặc 1.500.000 VNĐ')
                            ->required(),

                        Select::make('service_level')
                            ->label('Phân cấp dịch vụ')
                            ->options(Service::getLevels()) // Gọi từ Model Service
                            ->required()
                            ->default('Basic'),
                    ])->columns(2),
                
                Section::make('Chi tiết & Quyền lợi')
                    ->description('Mô tả và danh sách các quyền lợi đi kèm')
                    ->schema([
                        Textarea::make('description')
                            ->label('Mô tả ngắn')
                            ->placeholder('Mô tả thêm về phong cách hoặc lưu ý...')
                            ->columnSpanFull(),

                        TagsInput::make('features')
                            ->label('Các đặc điểm / Quyền lợi')
                            ->placeholder('Thêm đặc điểm (Nhấn Enter)')
                            ->helperText('VD: Tặng dán kích mí, Hỗ trợ làm tóc, Mỹ phẩm High-end...')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Hiển thị trên Web')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Hiển thị trên Trang chủ')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên dịch vụ')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price_text')
                    ->label('Giá')
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('service_level')
                    ->label('Phân cấp')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Basic' => 'gray',
                        'Premium' => 'warning',
                        'Extra' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->badge()
                    ->color('slate'),

                IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Hiện Trang chủ'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_level')
                    ->label('Lọc theo cấp độ')
                    ->options(Service::getLevels()),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Lọc theo danh mục')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}