<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortfolioResource\Pages;
use App\Filament\Resources\PortfolioResource\RelationManagers;
use App\Models\Portfolio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PortfolioResource extends Resource
{
    protected static ?string $model = Portfolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Quản lý Website';
    protected static ?string $navigationLabel = 'Bộ sưu tập';
    protected static ?string $modelLabel = 'Bộ sưu tập';
    protected static ?string $pluralModelLabel = 'Quản lý bộ sưu tập';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Thông tin Tác phẩm')
                    ->schema([
                        \Filament\Forms\Components\Select::make('category')
                            ->label('Danh mục')
                            ->options([
                                'bride' => 'Cô Dâu',
                                'event' => 'Sự Kiện',
                                'personal' => 'Kỷ Yếu / Cá Nhân',
                            ])
                            ->required(),
                            
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Định dạng tệp')
                            ->options([
                                'image' => 'Hình ảnh (JPG, PNG)',
                                'video' => 'Video (MP4 ngắn)',
                            ])
                            ->default('image')
                            ->live() // Tự động làm mới form khi bạn đổi giữa Ảnh và Video
                            ->required(),
    
                        \Filament\Forms\Components\FileUpload::make('file_path')
                            ->label('Tải tệp lên')
                            ->directory('portfolios')
                            // Cho phép nhận cả Ảnh và Video MP4 luôn để tránh lỗi kẹt bộ lọc
                            ->acceptedFileTypes(['image/*', 'video/mp4']) 
                            ->maxSize(20480) // Tối đa 20MB
                            ->required(),
                            
                        \Filament\Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề / Tên phong cách (Tùy chọn)'),
                            
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Hiển thị trên Web')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('title')->label('Tiêu đề'),
                \Filament\Tables\Columns\TextColumn::make('category')
                    ->label('Danh mục')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bride' => 'Cô Dâu',
                        'event' => 'Sự Kiện',
                        'personal' => 'Kỷ Yếu / Cá Nhân',
                        default => $state,
                    }),
                \Filament\Tables\Columns\TextColumn::make('type')->label('Loại')->badge(),
                \Filament\Tables\Columns\IconColumn::make('is_active')->label('Hiển thị')->boolean(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}
