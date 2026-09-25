<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class ThemeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Giao diện & Theme';
    protected static ?string $title = 'Tùy biến Giao diện, Màu sắc & Phông chữ';
    protected static ?string $slug = 'theme-settings';
    protected static ?int $navigationSort = 11;

    protected static string $view = 'filament.pages.theme-settings';

    public ?array $data = [];

    public static function getPresets(): array
    {
        return [
            'nude_luxury' => [
                'name' => 'Nude Luxury (Signature)',
                'description' => 'Tông màu kem nude, vàng đồng quý phái và nâu espresso cổ điển. Phong cách thương hiệu chuẩn.',
                'primary' => '#f6f1ec',
                'gold' => '#c8a98d',
                'dark' => '#3e2f2f',
                'button' => '#3e2f2f',
                'button_text' => '#ffffff',
                'heading_font' => 'Playfair Display',
                'body_font' => 'Inter',
            ],
            'rose_gold' => [
                'name' => 'Rose Gold Elegance',
                'description' => 'Hồng phấn pastel, vàng hồng lấp lánh và đỏ rượu vang sang trọng. Hoàn hảo cho Makeup Cô Dâu & Studio.',
                'primary' => '#faf0f2',
                'gold' => '#d49b9b',
                'dark' => '#4a2533',
                'button' => '#4a2533',
                'button_text' => '#ffffff',
                'heading_font' => 'Cormorant Garamond',
                'body_font' => 'Montserrat',
            ],
            'minimalist' => [
                'name' => 'Classic Minimalist',
                'description' => 'Trắng kem tinh khôi, ánh kim nhẹ và đen than hiện đại. Phong cách tối giản cao cấp quốc tế.',
                'primary' => '#f8f8f8',
                'gold' => '#cca466',
                'dark' => '#1a1a1a',
                'button' => '#1a1a1a',
                'button_text' => '#ffffff',
                'heading_font' => 'Cinzel',
                'body_font' => 'Plus Jakarta Sans',
            ],
            'emerald' => [
                'name' => 'Emerald Glamour',
                'description' => 'Kem ngọc bích, xanh lục bảo hoàng gia và vàng kim. Đẳng cấp, khác biệt và nổi bật.',
                'primary' => '#f4f8f6',
                'gold' => '#c9a050',
                'dark' => '#1d3b32',
                'button' => '#1d3b32',
                'button_text' => '#ffffff',
                'heading_font' => 'Lora',
                'body_font' => 'Be Vietnam Pro',
            ],
            'royal_velvet' => [
                'name' => 'Royal Velvet',
                'description' => 'Tím nhung pastel, vàng kim cổ điển và mận chín quý phái. Đậm chất nghệ thuật và cuốn hút.',
                'primary' => '#f7f5f8',
                'gold' => '#c6a664',
                'dark' => '#2b2038',
                'button' => '#2b2038',
                'button_text' => '#ffffff',
                'heading_font' => 'Prata',
                'body_font' => 'Nunito',
            ],
        ];
    }

    public static function getHeadingFonts(): array
    {
        return [
            'Playfair Display' => 'Playfair Display (Serif Cổ Điển - Sang Trọng)',
            'Cormorant Garamond' => 'Cormorant Garamond (Serif Quý Phái - Tinh Tế)',
            'Cinzel' => 'Cinzel (Serif Hoàng Gia - Đẳng Cấp)',
            'Lora' => 'Lora (Serif Hiện Đại - Dễ Đọc)',
            'Merriweather' => 'Merriweather (Serif Thanh Lịch - Đậm Nét)',
            'Montserrat' => 'Montserrat (Sans-Serif Mạnh Mẽ - Hiện Đại)',
            'Prata' => 'Prata (Serif Nghệ Thuật - Thời Trang)',
            'Bodoni Moda' => 'Bodoni Moda (Serif Tạp Chí Cao Cấp)',
            'Be Vietnam Pro' => 'Be Vietnam Pro (Sans Chuẩn Tiếng Việt)',
            'Plus Jakarta Sans' => 'Plus Jakarta Sans (Sans Tươi Mới)',
        ];
    }

    public static function getBodyFonts(): array
    {
        return [
            'Inter' => 'Inter (Sans-Serif Chuẩn UI/UX - Đọc Tốt Nhất)',
            'Plus Jakarta Sans' => 'Plus Jakarta Sans (Sans Trẻ Trung - Cao Cấp)',
            'Be Vietnam Pro' => 'Be Vietnam Pro (Sans Tối Ưu Dấu Tiếng Việt)',
            'Montserrat' => 'Montserrat (Sans Hiện Đại - Đa Dụng)',
            'Roboto' => 'Roboto (Sans Tiêu Chuẩn - Rõ Ràng)',
            'Open Sans' => 'Open Sans (Sans Dễ Đọc - Thân Thiện)',
            'Nunito' => 'Nunito (Sans Mềm Mại - Bo Tròn Nhẹ)',
            'Quicksand' => 'Quicksand (Sans Tròn Trị - Nhẹ Nhàng)',
        ];
    }

    public function mount(): void
    {
        $keys = [
            'theme_preset' => 'nude_luxury',
            'theme_color_primary' => '#f6f1ec',
            'theme_color_gold' => '#c8a98d',
            'theme_color_dark' => '#3e2f2f',
            'theme_color_button' => '#3e2f2f',
            'theme_color_button_text' => '#ffffff',
            'theme_font_heading_type' => 'google',
            'theme_font_heading' => 'Playfair Display',
            'theme_custom_heading_font_file' => null,
            'theme_font_body_type' => 'google',
            'theme_font_body' => 'Inter',
            'theme_custom_body_font_file' => null,
            'theme_custom_css' => null,
        ];

        $formData = [];
        foreach ($keys as $key => $default) {
            $formData[$key] = Setting::get($key, $default);
        }

        $this->form->fill($formData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('theme_preset'),

                Section::make('1. Bảng Màu Thương Hiệu (Color Palette Studio)')
                    ->description('Tùy chỉnh chi tiết từng màu sắc nhận diện của website.')
                    ->icon('heroicon-o-swatch')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 3,
                        ])->schema([
                            ColorPicker::make('theme_color_primary')
                                ->label('Màu nền chính (Primary Background)')
                                ->helperText('Màu nền xuyên suốt website.')
                                ->required()
                                ->live(),

                            ColorPicker::make('theme_color_gold')
                                ->label('Màu điểm nhấn (Accent / Gold)')
                                ->helperText('Màu viền, nhãn tag, icon và highlight.')
                                ->required()
                                ->live(),

                            ColorPicker::make('theme_color_dark')
                                ->label('Màu tối chủ đạo / Tiêu đề (Dark Text)')
                                ->helperText('Màu tiêu đề chính, footer và chữ tương phản.')
                                ->required()
                                ->live(),

                            ColorPicker::make('theme_color_button')
                                ->label('Màu nền nút bấm chính (Button Background)')
                                ->helperText('Màu nền các nút đặt lịch, xem chi tiết.')
                                ->required()
                                ->live(),

                            ColorPicker::make('theme_color_button_text')
                                ->label('Màu chữ nút bấm (Button Text Color)')
                                ->helperText('Màu chữ hiển thị trên nút bấm.')
                                ->required()
                                ->live(),
                        ]),
                    ]),

                Section::make('2. Phông Chữ & Tải Lên Font Riêng (Typography Studio)')
                    ->description('Lựa chọn từ kho Google Fonts hoặc tải lên file phông chữ việt hóa riêng của Studio.')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'lg' => 2,
                        ])->schema([
                            // CỘT 1: FONT TIÊU ĐỀ
                            Section::make('Phông chữ Tiêu đề (Heading / Serif)')
                                ->compact()
                                ->schema([
                                    Radio::make('theme_font_heading_type')
                                        ->label('Nguồn font tiêu đề')
                                        ->options([
                                            'google' => '✨ Kho Google Fonts có sẵn',
                                            'custom' => '📁 Tải lên file Font riêng (.woff2, .ttf, .otf)',
                                        ])
                                        ->default('google')
                                        ->live(),

                                    Select::make('theme_font_heading')
                                        ->label('Chọn phông chữ Tiêu đề')
                                        ->options(self::getHeadingFonts())
                                        ->default('Playfair Display')
                                        ->searchable()
                                        ->native(false)
                                        ->live()
                                        ->visible(fn (Get $get) => $get('theme_font_heading_type') === 'google'),

                                    FileUpload::make('theme_custom_heading_font_file')
                                        ->label('Tải file Font Tiêu đề (.woff2, .woff, .ttf, .otf)')
                                        ->disk('public')
                                        ->directory('fonts')
                                        ->acceptedFileTypes([
                                            'font/woff2', 'font/woff', 'font/ttf', 'font/otf',
                                            'application/font-woff', 'application/font-woff2',
                                            'application/x-font-ttf', 'application/x-font-truetype',
                                            'application/x-font-opentype', 'application/octet-stream',
                                        ])
                                        ->maxSize(10240)
                                        ->downloadable()
                                        ->openable()
                                        ->helperText('Hỗ trợ .woff2, .woff, .ttf, .otf (Tối đa 10MB). Hệ thống sẽ tự động nhúng @font-face.')
                                        ->visible(fn (Get $get) => $get('theme_font_heading_type') === 'custom')
                                        ->live(),
                                ]),

                            // CỘT 2: FONT NỘI DUNG
                            Section::make('Phông chữ Nội dung (Body / Sans)')
                                ->compact()
                                ->schema([
                                    Radio::make('theme_font_body_type')
                                        ->label('Nguồn font nội dung')
                                        ->options([
                                            'google' => '✨ Kho Google Fonts có sẵn',
                                            'custom' => '📁 Tải lên file Font riêng (.woff2, .ttf, .otf)',
                                        ])
                                        ->default('google')
                                        ->live(),

                                    Select::make('theme_font_body')
                                        ->label('Chọn phông chữ Nội dung')
                                        ->options(self::getBodyFonts())
                                        ->default('Inter')
                                        ->searchable()
                                        ->native(false)
                                        ->live()
                                        ->visible(fn (Get $get) => $get('theme_font_body_type') === 'google'),

                                    FileUpload::make('theme_custom_body_font_file')
                                        ->label('Tải file Font Nội dung (.woff2, .woff, .ttf, .otf)')
                                        ->disk('public')
                                        ->directory('fonts')
                                        ->acceptedFileTypes([
                                            'font/woff2', 'font/woff', 'font/ttf', 'font/otf',
                                            'application/font-woff', 'application/font-woff2',
                                            'application/x-font-ttf', 'application/x-font-truetype',
                                            'application/x-font-opentype', 'application/octet-stream',
                                        ])
                                        ->maxSize(10240)
                                        ->downloadable()
                                        ->openable()
                                        ->helperText('Hỗ trợ .woff2, .woff, .ttf, .otf (Tối đa 10MB). Hệ thống sẽ tự động nhúng @font-face.')
                                        ->visible(fn (Get $get) => $get('theme_font_body_type') === 'custom')
                                        ->live(),
                                ]),
                        ]),
                    ]),

                Section::make('3. Tùy Biến CSS Nâng Cao (Custom CSS)')
                    ->description('Dành cho nhà thiết kế muốn chèn thêm mã CSS tùy chỉnh đặc thù.')
                    ->icon('heroicon-o-code-bracket')
                    ->collapsed()
                    ->schema([
                        Textarea::make('theme_custom_css')
                            ->label('Mã CSS tùy chỉnh')
                            ->rows(4)
                            ->placeholder("/* Ví dụ tùy biến thêm */\n.custom-element {\n    letter-spacing: 0.2em;\n}")
                            ->helperText('Mã CSS này sẽ được chèn trực tiếp vào phần <head> trên mọi trang của website.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function applyPreset(string $presetKey): void
    {
        $presets = self::getPresets();
        if (isset($presets[$presetKey])) {
            $p = $presets[$presetKey];
            $this->data['theme_preset'] = $presetKey;
            $this->data['theme_color_primary'] = $p['primary'];
            $this->data['theme_color_gold'] = $p['gold'];
            $this->data['theme_color_dark'] = $p['dark'];
            $this->data['theme_color_button'] = $p['button'];
            $this->data['theme_color_button_text'] = $p['button_text'];
            $this->data['theme_font_heading_type'] = 'google';
            $this->data['theme_font_heading'] = $p['heading_font'];
            $this->data['theme_font_body_type'] = 'google';
            $this->data['theme_font_body'] = $p['body_font'];

            $this->form->fill($this->data);

            Notification::make()
                ->title("Đã chọn mẫu: {$p['name']}")
                ->body("Thông số màu sắc & phông chữ đã được nạp vào bộ chỉnh sửa. Nhấn nút 'Lưu & Cập Nhật Giao Diện' bên dưới để áp dụng vào website.")
                ->info()
                ->send();
        }
    }

    public function submit(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'group' => 'theme',
                    'value' => $value,
                    'type' => str_contains($key, 'css') ? 'textarea' : 'text',
                ]
            );
        }

        Cache::forget('site_settings');

        Notification::make()
            ->title('Đã lưu cấu hình Giao diện, Màu sắc & Phông chữ thành công!')
            ->body('Giao diện website phía người dùng đã được cập nhật theo bảng màu và phông chữ mới.')
            ->success()
            ->send();
    }
}
