<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Filament\Resources\BannerResource\RelationManagers;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Quản lý Website';
    protected static ?string $navigationLabel = 'Banner quảng cáo';
    protected static ?string $modelLabel = 'Banner';
    protected static ?string $pluralModelLabel = 'Quản lý Banner';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Hình ảnh Banner')
                    ->description('Quản lý hình ảnh Slider hoặc Popup quảng cáo')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề Banner')
                            ->required(),
                        \Filament\Forms\Components\FileUpload::make('image_path')
                            ->label('Hình ảnh (Desktop)')
                            ->image()
                            ->directory('banners') // Ảnh sẽ được lưu vào thư mục storage/app/public/banners
                            ->required(),
                        \Filament\Forms\Components\Select::make('position')
                            ->label('Vị trí hiển thị')
                            ->options([
                                'home_hero' => 'Slider đầu trang chủ',
                                'popup' => 'Popup khuyến mãi',
                            ])
                            ->default('home_hero')
                            ->native(false),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label('Hiển thị')
                            ->default(true),
                        \Filament\Forms\Components\TextInput::make('order')
                            ->label('Thứ tự')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('image_path')
                    ->label('Ảnh preview')
                    ->circular(),
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('position')
                    ->label('Vị trí')
                    ->badge(),
                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean(),
            ])
            ->filters([])
            ->actions([\Filament\Tables\Actions\EditAction::make()])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([\Filament\Tables\Actions\DeleteBulkAction::make()])]);
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
