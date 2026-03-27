<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $modelLabel = 'Tài khoản';
    protected static ?string $pluralModelLabel = 'Danh sách Tài khoản';
    
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\Grid::make(3)->schema([
                // CỘT TRÁI: Chứa 3 ô nhập liệu (chiếm 2/3 diện tích)
                \Filament\Forms\Components\Group::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Họ tên'),
                    
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->required(),
                    
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->label('Mật khẩu'),
                ])->columnSpan(2),

                // CỘT PHẢI: Chứa Avatar (chiếm 1/3 diện tích)
                \Filament\Forms\Components\Group::make()->schema([
                    Forms\Components\FileUpload::make('avatar')
                        ->label('Ảnh đại diện')
                        ->directory('avatars')
                        ->image()
                        ->imageResizeTargetWidth(500)
                        ->maxSize(5120)
                        ->avatar()
                        ->extraAttributes(['class' => 'custom-big-avatar']), // Gắn class nhận diện

                    // Nhúng trực tiếp CSS để ghi đè kích thước gốc của Filament
                    \Filament\Forms\Components\Placeholder::make('css')
                        ->hiddenLabel()
                        ->content(new \Illuminate\Support\HtmlString('
                            <style>
                                .custom-big-avatar {
                                    margin-left: 3rem !important; /* Đẩy khoảng cách ra xa cột trái */
                                }
                                .custom-big-avatar [x-ref="fileUpload"], 
                                .custom-big-avatar .filepond--root { 
                                    width: 250px !important; 
                                    height: 250px !important; 
                                    margin: 0 auto !important; /* Tự động căn giữa khung ảnh ở cột phải */
                                }
                                .custom-big-avatar header {
                                    justify-content: center; /* Căn giữa dòng chữ "Ảnh đại diện" */
                                }
                            </style>
                        ')),
                ])->columnSpan(1),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('avatar')
                ->label('Avatar')
                ->circular(),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\IconColumn::make('email_verified_at')
                ->label('Trạng thái')
                ->boolean()
                ->default(false), // Hiện dấu X đỏ nếu chưa xác minh
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
