<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Quản lý Website';
    protected static ?string $navigationLabel = 'Tạp chí';
    protected static ?string $modelLabel = 'Bài viết tạp chí';
    protected static ?string $pluralModelLabel = 'Quản lý Tạp chí';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nội dung bài viết')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label('Đường dẫn (Slug)')
                            ->required()
                            ->unique(Post::class, 'slug', ignoreRecord: true),

                        Forms\Components\Textarea::make('excerpt')
                            ->label('Mô tả ngắn (Tóm tắt)')
                            ->rows(3)
                            ->required(),

                        // CẤU HÌNH RICH EDITOR ĐÃ SỬA LỖI HIỂN THỊ ẢNH
                        Forms\Components\RichEditor::make('content')
                            ->label('Nội dung chi tiết')
                            ->fileAttachmentsDisk('public') // Ép buộc dùng đĩa public
                            ->fileAttachmentsDirectory('posts/content') // Thư mục lưu ảnh
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull()
                            ->required(),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Thông tin bổ sung')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label('Danh mục')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\FileUpload::make('thumbnail')
                                    ->label('Ảnh bìa bài viết')
                                    ->image()
                                    ->directory('posts/thumbnails')
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Trạng thái')
                                    ->options([
                                        'draft' => 'Bản nháp',
                                        'published' => 'Xuất bản',
                                    ])
                                    ->default('published')
                                    ->required(),

                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('Ngày đăng')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Bài viết nổi bật')
                                    ->default(false),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Ảnh'),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->badge(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Ngày đăng')
                    ->dateTime('d/m/Y')
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}