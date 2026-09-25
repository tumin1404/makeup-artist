<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Cấu hình website';
    protected static ?string $modelLabel = 'Cấu hình';
    protected static ?string $pluralModelLabel = 'Cấu hình hệ thống';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin định danh phần tử (Element Identifier)')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\Select::make('group')
                            ->label('Phân nhóm trang / Chức năng')
                            ->options([
                                'general' => '1. Cấu hình chung & SEO (Logo, Favicon, Meta)',
                                'contact' => '2. Thông tin liên hệ & Địa chỉ (Hotline, Email, Chi nhánh, Bản đồ)',
                                'social' => '3. Mạng xã hội (Facebook, Instagram, TikTok, Zalo, YouTube)',
                                'home' => '4. Trang chủ (Hero Subtitle, Slogan, Trước/Sau, Tạp chí)',
                                'about' => '5. Trang Giới thiệu & Bio (Banner, Story, Chữ ký, Thống kê, Timeline, Video)',
                                'services' => '6. Trang Dịch vụ & Báo giá (Banner, Bảng giá, Quy trình 4 bước)',
                                'portfolio' => '7. Trang Bộ sưu tập (Banner, Tiêu đề, Mô tả Gallery)',
                                'blog' => '8. Trang Tạp chí & Tác giả (Banner, Tiêu đề, Tác giả, Avatar, Bio)',
                                'booking' => '9. Trang Đặt lịch & Lời nhắn (Tiêu đề, Lưu ý, Gợi ý, Thông báo)',
                                'banking' => '10. Thanh toán & Hóa đơn (Ngân hàng, STK, Chủ TK, Mã VietQR)',
                                'popup' => '11. Popup khuyến mãi (Bật/Tắt, Banner, Tiêu đề, Link)',
                                'theme' => '12. Giao diện & Chủ đề (Theme, Màu sắc & Font chữ)',
                                'mail' => '13. Máy chủ Email & SMTP (Domain Mail, Gmail, Resend)',
                                'telegram' => '14. Thông báo Telegram Bot (Thông báo đặt lịch di động)',
                                'zalo' => '15. Thông báo Zalo ZNS (Zalo Doanh nghiệp)',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('type')
                            ->label('Loại dữ liệu nhập (Content Type)')
                            ->options([
                                'text' => '📝 Văn bản ngắn (Text / Input)',
                                'textarea' => '📄 Đoạn văn bản (Textarea / Multi-line)',
                                'rich_editor' => '✨ Trình soạn thảo văn bản (Rich Text / HTML)',
                                'image' => '🖼️ Tải hình ảnh (Image Upload)',
                                'video' => '🎬 Đường dẫn Video (YouTube / MP4 URL)',
                                'toggle' => '🔘 Công tắc Bật/Tắt (Toggle Active/Inactive)',
                            ])
                            ->default('text')
                            ->required()
                            ->live()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('key')
                            ->label('Từ khóa code (Key code)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->helperText('Mã code duy nhất dùng trong giao diện website ($settings[\'...\']).')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('description')
                            ->label('Tên hiển thị / Mô tả phần tử')
                            ->required()
                            ->placeholder('Ví dụ: Ảnh trước khi trang điểm (Before)')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('recommendation')
                            ->label('Khuyến nghị kỹ thuật & Kích thước / Tỷ lệ chuẩn')
                            ->rows(2)
                            ->placeholder('Ví dụ: Khuyến nghị: Ảnh dọc tỷ lệ 9:16, kích thước chuẩn 1080x1920px.')
                            ->columnSpanFull()
                            ->helperText('Ghi chú kích thước và định dạng chuẩn để quản trị viên tải lên đúng tỷ lệ giao diện.'),
                    ])->columns(2),

                Forms\Components\Section::make('Nội dung giá trị cấu hình (Setting Value)')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        // Text input
                        Forms\Components\TextInput::make('value')
                            ->label('Nội dung văn bản')
                            ->placeholder('Nhập nội dung...')
                            ->helperText(function (Get $get, ?Setting $record) {
                                $rec = $get('recommendation') ?? ($record?->recommendation);
                                return $rec ? '💡 Khuyến nghị: ' . $rec : 'Định dạng: Văn bản ngắn một dòng.';
                            })
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => in_array($get('type'), ['text', null])),

                        // Textarea input
                        Forms\Components\Textarea::make('value')
                            ->label('Nội dung đoạn văn')
                            ->rows(5)
                            ->placeholder('Nhập đoạn văn bản...')
                            ->helperText(function (Get $get, ?Setting $record) {
                                $rec = $get('recommendation') ?? ($record?->recommendation);
                                return $rec ? '💡 Khuyến nghị: ' . $rec : 'Định dạng: Đoạn văn bản nhiều dòng.';
                            })
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === 'textarea'),

                        // Rich editor input
                        Forms\Components\RichEditor::make('value')
                            ->label('Nội dung định dạng phong phú')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'h2', 'h3', 'bulletList', 'orderedList',
                                'link', 'blockquote', 'undo', 'redo',
                            ])
                            ->helperText(function (Get $get, ?Setting $record) {
                                $rec = $get('recommendation') ?? ($record?->recommendation);
                                return $rec ? '💡 Khuyến nghị: ' . $rec : 'Định dạng: Soạn thảo văn bản định dạng HTML.';
                            })
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === 'rich_editor'),

                        // Image upload input with live preview & format advice
                        Forms\Components\FileUpload::make('value')
                            ->label('Tải hình ảnh lên từ máy tính')
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/svg+xml', 'image/jpeg', 'image/webp'])
                            ->maxSize(10240)
                            ->openable()
                            ->downloadable()
                            ->previewable()
                            ->helperText(function (Get $get, ?Setting $record) {
                                $rec = $get('recommendation') ?? ($record?->recommendation);
                                $recPrefix = $rec ? "<p class='text-amber-600 dark:text-amber-400 font-medium'>💡 Khuyến nghị: {$rec}</p>" : '';
                                return new HtmlString("
                                    <div class='space-y-1 text-xs text-gray-500 dark:text-gray-400 mt-1'>
                                        {$recPrefix}
                                        <p>📁 Định dạng hỗ trợ: <strong>PNG, JPG, WEBP, SVG, ICO</strong> (Tối đa 10MB). Hệ thống sẽ tự động tối ưu hóa nén ảnh chạy nền.</p>
                                    </div>
                                ");
                            })
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === 'image'),

                        // Video link input
                        Forms\Components\TextInput::make('value')
                            ->label('Đường dẫn Video (YouTube URL hoặc Link trực tiếp)')
                            ->prefixIcon('heroicon-o-video-camera')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText(function (Get $get, ?Setting $record) {
                                $rec = $get('recommendation') ?? ($record?->recommendation);
                                return $rec ? '💡 Khuyến nghị: ' . $rec . ' — Hỗ trợ link video YouTube hoặc đường dẫn file MP4 trực tiếp.' : 'Hỗ trợ link video YouTube hoặc đường dẫn file MP4 trực tiếp.';
                            })
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === 'video'),

                        // Toggle active/inactive input
                        Forms\Components\Select::make('value')
                            ->label('Trạng thái kích hoạt')
                            ->options([
                                '1' => '🟢 Bật (Hiển thị phần tử trên website)',
                                '0' => '⚪ Tắt (Ẩn phần tử khỏi website)',
                            ])
                            ->default('1')
                            ->native(false)
                            ->helperText(function (Get $get, ?Setting $record) {
                                $rec = $get('recommendation') ?? ($record?->recommendation);
                                return $rec ? '💡 Khuyến nghị: ' . $rec : 'Bật để hiển thị hoặc Tắt để ẩn phần tử này khỏi website.';
                            })
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === 'toggle'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'general' => 'Chung & SEO',
                        'contact' => 'Liên hệ',
                        'social' => 'Mạng xã hội',
                        'home' => 'Trang chủ',
                        'about' => 'Giới thiệu',
                        'services' => 'Dịch vụ',
                        'portfolio' => 'Bộ sưu tập',
                        'blog' => 'Tạp chí',
                        'booking' => 'Đặt lịch',
                        'banking' => 'Thanh toán',
                        'popup' => 'Popup',
                        'theme' => 'Giao diện',
                        'mail' => 'Email / SMTP',
                        'telegram' => 'Telegram Bot',
                        'zalo' => 'Zalo ZNS',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'contact' => 'success',
                        'social' => 'info',
                        'home' => 'primary',
                        'about' => 'warning',
                        'services' => 'danger',
                        'portfolio' => 'secondary',
                        'blog' => 'info',
                        'booking' => 'primary',
                        'banking' => 'danger',
                        'popup' => 'warning',
                        'theme' => 'success',
                        'mail' => 'warning',
                        'telegram' => 'info',
                        'zalo' => 'primary',
                        default => 'gray',
                    })
                    ->label('Nhóm trang'),

                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Setting $record): string => $record->key)
                    ->label('Tên phần tử & Mã Key'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'image' => '🖼️ Ảnh',
                        'video' => '🎬 Video',
                        'rich_editor' => '✨ Rich Text',
                        'textarea' => '📄 Đoạn văn',
                        'toggle' => '🔘 Bật/Tắt',
                        default => '📝 Chữ',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'warning',
                        'video' => 'danger',
                        'rich_editor' => 'purple',
                        'toggle' => 'success',
                        default => 'gray',
                    })
                    ->label('Loại'),

                Tables\Columns\TextColumn::make('value')
                    ->searchable()
                    ->wrap()
                    ->label('Giá trị cấu hình')
                    ->formatStateUsing(function (Setting $record, $state) {
                        if ($record->type === 'image') {
                            if (empty($record->value)) {
                                return '<span style="font-size: 11px; color: #9ca3af; font-style: italic;">Chưa tải ảnh</span>';
                            }
                            $src = str_starts_with($record->value, 'http') ? $record->value : asset('storage/' . $record->value);
                            $filename = e(basename($record->value));
                            return '
                                <div style="display: inline-flex; align-items: center; gap: 8px; max-width: 250px; vertical-align: middle;">
                                    <a href="' . $src . '" target="_blank" style="display: block; flex-shrink: 0; width: 44px; height: 44px; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.05); background: #f3f4f6;">
                                        <img src="' . $src . '" alt="Thumbnail" width="44" height="44" style="width: 44px; height: 44px; min-width: 44px; max-width: 44px; min-height: 44px; max-height: 44px; object-fit: cover; display: block;" />
                                    </a>
                                    <div style="font-size: 11px; color: #6b7280; font-family: monospace; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 170px;" title="' . $filename . '">' . $filename . '</div>
                                </div>
                            ';
                        }

                        if ($record->type === 'video') {
                            if (empty($record->value)) {
                                return '<span style="font-size: 11px; color: #9ca3af; font-style: italic;">Chưa có link video</span>';
                            }
                            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $record->value, $match);
                            if (!empty($match[1])) {
                                $ytId = $match[1];
                                $thumb = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
                                return '
                                    <div style="display: inline-flex; align-items: center; gap: 8px; max-width: 250px; vertical-align: middle;">
                                        <a href="' . e($record->value) . '" target="_blank" style="display: block; position: relative; flex-shrink: 0; width: 56px; height: 36px; border-radius: 6px; overflow: hidden; border: 1px solid #e5e7eb; background: #000;">
                                            <img src="' . $thumb . '" width="56" height="36" style="width: 56px; height: 36px; min-width: 56px; max-width: 56px; min-height: 36px; max-height: 36px; object-fit: cover; display: block;" />
                                            <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.35); display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 10px;">▶</div>
                                        </a>
                                        <a href="' . e($record->value) . '" target="_blank" style="font-size: 11px; color: #2563eb; text-decoration: underline; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 160px;" title="' . e($record->value) . '">' . e(Str::limit($record->value, 25)) . '</a>
                                    </div>
                                ';
                            }
                            return '<a href="' . e($record->value) . '" target="_blank" style="font-size: 11px; color: #2563eb; text-decoration: underline; display: inline-flex; align-items: center; gap: 4px;">🎬 ' . e(Str::limit($record->value, 30)) . '</a>';
                        }

                        if ($record->type === 'toggle') {
                            return $state === '1' ? '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">🟢 Đang bật</span>' : '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">⚪ Đang tắt</span>';
                        }

                        return '<span class="line-clamp-2 text-sm text-gray-800 dark:text-gray-200">' . e(Str::limit(strip_tags((string)$state), 70)) . '</span>';
                    })
                    ->html(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general' => '1. Chung & SEO',
                        'contact' => '2. Liên hệ & Địa chỉ',
                        'social' => '3. Mạng xã hội',
                        'home' => '4. Trang chủ',
                        'about' => '5. Trang Giới thiệu',
                        'services' => '6. Dịch vụ & Báo giá',
                        'portfolio' => '7. Bộ sưu tập',
                        'blog' => '8. Tạp chí & Tác giả',
                        'booking' => '9. Đặt lịch & Lời nhắn',
                        'banking' => '10. Thanh toán & Hóa đơn',
                        'popup' => '11. Popup khuyến mãi',
                        'theme' => '12. Giao diện & Chủ đề',
                        'mail' => '13. Máy chủ Email & SMTP',
                        'telegram' => '14. Thông báo Telegram Bot',
                        'zalo' => '15. Thông báo Zalo ZNS',
                    ])
                    ->label('Lọc theo nhóm'),

                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'text' => 'Văn bản ngắn (Text)',
                        'textarea' => 'Đoạn văn (Textarea)',
                        'rich_editor' => 'Trình soạn thảo (Rich Editor)',
                        'image' => 'Hình ảnh (Image)',
                        'video' => 'Video (YouTube)',
                        'toggle' => 'Bật / Tắt (Toggle)',
                    ])
                    ->label('Lọc theo loại dữ liệu'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->label('Chỉnh sửa')
                    ->icon('heroicon-o-pencil-square'),
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