<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Cấu hình';
    protected static ?string $pluralModelLabel = 'Cấu hình chung';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Thay vì dùng Select cứng, ta dùng TextInput với Datalist
                Forms\Components\TextInput::make('group')
                    ->datalist(fn () => \App\Models\Setting::pluck('group')->unique()->toArray())
                    ->required()
                    ->placeholder('Chọn nhóm có sẵn hoặc gõ tên nhóm mới...')
                    ->label('Nhóm'),

                Forms\Components\Select::make('type')
                    ->options([
                        'text' => 'Văn bản (Hoặc dán Link ảnh)',
                        'textarea' => 'Đoạn văn',
                        'rich_editor' => 'Trình soạn thảo',
                        'image' => 'Tải ảnh từ máy tính',
                    ])
                    ->default('text')
                    ->required()
                    ->live() // Kích hoạt form động
                    ->label('Loại dữ liệu'),

                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Từ khóa (Key)'),

                // Các ô Value tự động ẩn/hiện dựa theo Loại dữ liệu
                Forms\Components\TextInput::make('value')
                    ->label('Giá trị')
                    ->required()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'text'),

                Forms\Components\Textarea::make('value')
                    ->label('Giá trị')
                    ->required()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'textarea'),

                Forms\Components\RichEditor::make('value')
                    ->label('Giá trị')
                    ->required()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'rich_editor'),

                Forms\Components\FileUpload::make('value')
                    ->label('Tải ảnh lên')
                    ->image()
                    ->directory('settings') // Lưu vào thư mục storage/app/public/settings
                    ->required()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'image'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable()->label('Từ khóa'),
                Tables\Columns\TextColumn::make('value')->limit(50)->searchable()->label('Giá trị'),
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->sortable()
                    ->color(function (string $state) {
                        // 1. Giữ nguyên màu cho các nhóm mặc định
                        $defaults = [
                            'general' => 'gray',
                            'contact' => 'success',
                            'social' => 'info',
                            'info' => 'warning',
                            'stats' => 'danger',
                            'timeline' => 'primary',
                            'popup' => 'success',
                        ];

                        if (isset($defaults[$state])) {
                            return $defaults[$state];
                        }

                        // 2. Danh sách bảng màu đẹp mở rộng của Filament cho các nhóm mới
                        $colors = [
                            \Filament\Support\Colors\Color::Pink,
                            \Filament\Support\Colors\Color::Purple,
                            \Filament\Support\Colors\Color::Indigo,
                            \Filament\Support\Colors\Color::Teal,
                            \Filament\Support\Colors\Color::Orange,
                            \Filament\Support\Colors\Color::Rose,
                            \Filament\Support\Colors\Color::Cyan,
                            \Filament\Support\Colors\Color::Emerald,
                        ];

                        // 3. Chuyển tên nhóm thành số và gán 1 màu cố định từ danh sách trên
                        return $colors[abs(crc32($state)) % count($colors)];
                    })
                    ->label('Nhóm'),
            ])
            ->filters([
                // Thêm bộ lọc nhanh trên góc phải
                Tables\Filters\SelectFilter::make('group')
                ->options(fn () => \App\Models\Setting::pluck('group', 'group')->unique()->toArray())
                ->label('Lọc theo nhóm'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}