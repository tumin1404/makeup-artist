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
use App\Models\Category;

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
                        Forms\Components\Select::make('category_id')
                            ->label('Danh mục')
                            ->relationship(
                                name: 'category', 
                                titleAttribute: 'name', 
                                // Chỉ lấy các danh mục dành cho Portfolio
                                modifyQueryUsing: fn (Builder $query) => $query->where('type', Category::TYPE_PORTFOLIO)
                            )
                            ->searchable()
                            ->preload()
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
                            ->imageResizeTargetWidth(1080) // Tự động thu nhỏ ảnh lớn xuống tối đa ngang 1080px
                            ->maxSize(15360)
                            ->panelAspectRatio('2:1') // THÊM DÒNG NÀY: Khóa cứng tỷ lệ khung
                            ->panelLayout('integrated')
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
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Hình ảnh')
                    ->size(50) // Kích thước thumbnail vuông 50px
                    ->disk('public') // Thêm dòng này để ép Filament đọc đúng thư mục
                    ->circular(),
                \Filament\Tables\Columns\TextColumn::make('title')->label('Tiêu đề'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->badge()
                    ->color('info'),
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
