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
                    ->live()
                    ->label('Loại dữ liệu'),

                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->label('Từ khóa (Key)'),

                Forms\Components\TextInput::make('description')
                    ->label('Mô tả chức năng (Tiếng Việt)')
                    ->required()
                    ->placeholder('Ví dụ: Bật/Tắt thông báo trang chủ')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('value')
                    ->label('Giá trị cài đặt')
                    ->helperText(fn (\Filament\Forms\Get $get) => $get('description'))
                    ->required()
                    ->columnSpanFull()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'text'),

                Forms\Components\Textarea::make('value')
                    ->label('Giá trị cài đặt')
                    ->helperText(fn (\Filament\Forms\Get $get) => $get('description'))
                    ->required()
                    ->columnSpanFull()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'textarea'),

                Forms\Components\RichEditor::make('value')
                    ->label('Giá trị cài đặt')
                    ->helperText(fn (\Filament\Forms\Get $get) => $get('description'))
                    ->required()
                    ->columnSpanFull()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'rich_editor'),

                // ĐÃ CẬP NHẬT: Hỗ trợ thêm định dạng Favicon và giới hạn dung lượng
                Forms\Components\FileUpload::make('value')
                    ->label('Tải ảnh lên')
                    ->helperText(fn (\Filament\Forms\Get $get) => $get('description'))
                    ->image()
                    ->directory('settings')
                    ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/svg+xml', 'image/jpeg', 'image/webp']) 
                    ->maxSize(2048) // Tối đa 2MB
                    ->required()
                    ->columnSpanFull()
                    ->visible(fn (\Filament\Forms\Get $get) => $get('type') === 'image'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->sortable()
                    ->color(function (string $state) {
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

                        return $colors[abs(crc32($state)) % count($colors)];
                    })
                    ->label('Nhóm'),

                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->fontFamily('mono')
                    ->label('Từ khóa'),

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->label('Mô tả chức năng'),

                Tables\Columns\TextColumn::make('value')
                    ->limit(50)
                    ->searchable()
                    ->label('Giá trị hiện tại'),
            ])
            ->filters([
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
            ])
            ->defaultSort('group', 'asc');
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