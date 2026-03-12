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
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Từ khóa (Key)'),
                Forms\Components\Textarea::make('value')
                    ->required()
                    ->label('Giá trị (Value)'),
                Forms\Components\Select::make('group')
                    ->options([
                        'general' => 'Chung',
                        'contact' => 'Liên hệ',
                        'social' => 'Mạng xã hội',
                    ])
                    ->default('general')
                    ->required()
                    ->label('Nhóm'),
                Forms\Components\Select::make('type')
                    ->options([
                        'text' => 'Văn bản',
                        'textarea' => 'Đoạn văn',
                        'image' => 'Hình ảnh',
                    ])
                    ->default('text')
                    ->required()
                    ->label('Loại dữ liệu'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')->searchable()->label('Từ khóa'),
                Tables\Columns\TextColumn::make('value')->limit(50)->label('Giá trị'),
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'contact' => 'success',
                        'social' => 'info',
                        default => 'primary',
                    })->label('Nhóm'),
            ])
            ->filters([])
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