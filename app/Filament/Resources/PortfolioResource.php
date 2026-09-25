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
                                modifyQueryUsing: fn (Builder $query) => $query->where('type', Category::TYPE_PORTFOLIO)
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Định dạng tệp')
                            ->options([
                                'image' => '🖼️ Hình ảnh (Tự động nén WebP)',
                                'video' => '🎬 Video ngắn (Tự động nén Silent Micro-Video & tạo Poster)',
                            ])
                            ->default('image')
                            ->live()
                            ->required(),
    
                        \Filament\Forms\Components\FileUpload::make('file_path')
                            ->label('Tải tệp lên')
                            ->directory('portfolios')
                            ->acceptedFileTypes(['image/*', 'video/mp4', 'video/quicktime', 'video/webm']) 
                            ->maxSize(51200) // 50MB
                            ->panelAspectRatio('2:1')
                            ->panelLayout('integrated')
                            ->helperText('Hệ thống sẽ tự động tối ưu hóa: Ảnh chuyển thành .WebP (giảm 85%), Video chuyển thành định dạng Silent mượt mà không tiếng (giảm 98% dung lượng).')
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
                Tables\Columns\ImageColumn::make('preview')
                    ->label('Ảnh / Poster')
                    ->state(function (Portfolio $record) {
                        if ($record->type === 'video') {
                            if (!empty($record->poster_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($record->poster_path)) {
                                return $record->poster_path;
                            }
                            // Check if poster file exists on disk
                            if (!empty($record->file_path)) {
                                $pathInfo = pathinfo($record->file_path);
                                $targetDir = ($pathInfo['dirname'] !== '.' && $pathInfo['dirname'] !== '') ? $pathInfo['dirname'] . '/' : '';
                                $possiblePoster = $targetDir . $pathInfo['filename'] . '_poster.webp';
                                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($possiblePoster)) {
                                    return $possiblePoster;
                                }

                                // Attempt on-the-fly poster extraction
                                $extracted = app(\App\Services\Media\MediaOptimizerService::class)->extractPoster($record->file_path);
                                if ($extracted) {
                                    $record->poster_path = $extracted;
                                    $record->saveQuietly();
                                    return $extracted;
                                }
                            }
                            return null;
                        }
                        return $record->file_path;
                    })
                    ->size(56)
                    ->disk('public')
                    ->square()
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover shadow-sm border border-gray-200']),
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->placeholder('(Không có tiêu đề)'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->badge()
                    ->color('info'),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'video' ? '🎬 Video' : '🖼️ Ảnh')
                    ->color(fn (string $state) => $state === 'video' ? 'warning' : 'primary')
                    ->label('Loại'),
                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Hiển thị'),
            ])
            ->filters([
                //
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
