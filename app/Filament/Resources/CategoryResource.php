<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Quản lý Website';
    protected static ?string $navigationLabel = 'Danh mục chung';
    protected static ?string $modelLabel = 'Danh mục';
    protected static ?string $pluralModelLabel = 'Quản lý Danh mục';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin danh mục')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên danh mục')
                            ->required()
                            ->live(onBlur: true) // Lắng nghe sự thay đổi khi rời ô nhập liệu
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ), // Tự động tạo slug khi tạo mới
                        
                        TextInput::make('slug')
                            ->label('Đường dẫn (Slug)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('Dùng để tạo đường dẫn web đẹp, không nên chứa dấu tiếng Việt.'),

                        Select::make('type')
                            ->label('Loại danh mục')
                            ->options([
                                'service' => 'Dùng cho Dịch vụ',
                                'portfolio' => 'Dùng cho Portfolio (Ảnh)',
                                'post' => 'Dùng cho Tạp chí (Bài viết)',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('order')
                            ->label('Thứ tự sắp xếp')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('type')
                    ->label('Phân loại')
                    ->badge() // Biến thành dạng nhãn màu
                    ->color(fn (string $state): string => match ($state) {
                        'service' => 'success',
                        'portfolio' => 'info',
                        'post' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'service' => 'Dịch vụ',
                        'portfolio' => 'Portfolio',
                        'post' => 'Tạp chí',
                        default => $state,
                    }),

                TextColumn::make('order')
                    ->label('Thứ tự')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('type') // Sắp xếp theo loại cho dễ nhìn
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Lọc theo loại')
                    ->options([
                        'service' => 'Dịch vụ',
                        'portfolio' => 'Portfolio',
                        'post' => 'Tạp chí',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}