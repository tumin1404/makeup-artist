<?php

namespace App\Filament\Pages;

use App\Helpers\InvoiceHelper;
use App\Models\Setting;
use App\Services\InvoiceConfigService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class InvoiceSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Hệ thống';
    protected static ?string $navigationLabel = 'Thiết kế & Mẫu hóa đơn';
    protected static ?string $title = 'Tùy biến Cấu hình & Mẫu Hóa Đơn';
    protected static ?string $slug = 'invoice-settings';
    protected static ?int $navigationSort = 12;

    protected static string $view = 'filament.pages.invoice-settings';

    public ?array $data = [];
    public string $activePreset = 'luxury_service';

    public function mount(): void
    {
        $config = InvoiceConfigService::getCurrentConfig();
        $this->activePreset = $config['active_preset'] ?? 'luxury_service';
        $this->form->fill($config);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cài Đặt Chung & Phong Cách Hóa Đơn')
                    ->icon('heroicon-o-paint-brush')
                    ->description('Tùy chỉnh khổ giấy in và phong cách màu sắc thương hiệu')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('paper_size')
                                ->label('Khổ giấy in ấn')
                                ->options([
                                    'a4' => '📄 Khổ A4 Dọc (Chuẩn văn phòng / Doanh nghiệp)',
                                    'a5' => '📑 Khổ A5 Ngang (Gọn gàng / Tiết kiệm giấy)',
                                    'k80' => '🧾 Khổ Bill Nhiệt K80 (80mm - Máy in POS mini)',
                                ])
                                ->required()
                                ->native(false)
                                ->live(),

                            Select::make('color_theme')
                                ->label('Phong cách màu sắc')
                                ->options([
                                    'luxury' => '🌟 Luxury Gold & Dark (Quý phái / Studio)',
                                    'classic' => '🏢 Classic Neutral (Truyền thống / Trang trọng)',
                                    'corporate' => '🏛️ Corporate Blue & Emerald (Doanh nghiệp)',
                                    'minimal' => '🖤 Minimal Black & White (Tối giản / Sắc nét)',
                                ])
                                ->required()
                                ->native(false)
                                ->live(),

                            Select::make('signature_type')
                                ->label('Kiểu Chữ ký & Xác thực')
                                ->options([
                                    'two_parties' => '✍️ 2 Bên (Khách hàng - Người lập phiếu)',
                                    'five_parties' => '📋 5 Chức danh (Người lập - Nhận - Thủ kho - Kế toán - GĐ)',
                                    'digital_stamp' => '🛡️ Dấu Chữ ký điện tử (Signature Valid)',
                                    'none' => '🚫 Không hiển thị chữ ký',
                                ])
                                ->required()
                                ->native(false)
                                ->live(),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('1. Thông Tin Đơn Vị Bán Hàng & Logo')
                    ->icon('heroicon-o-building-storefront')
                    ->description('Bật/tắt và tùy chỉnh thông tin hiển thị của Studio / Doanh nghiệp')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('show_logo')
                                ->label('Hiển thị Logo')
                                ->helperText('Sử dụng logo cấu hình từ cài đặt chung website')
                                ->live(),

                            Toggle::make('show_seller_name')
                                ->label('Hiển thị Tên Studio / Công ty')
                                ->live(),

                            Toggle::make('show_seller_tax')
                                ->label('Hiển thị Mã số thuế bên bán (MST)')
                                ->live(),

                            Toggle::make('show_seller_address')
                                ->label('Hiển thị Địa chỉ trụ sở / Chi nhánh')
                                ->live(),

                            Toggle::make('show_seller_phone')
                                ->label('Hiển thị Hotline / Số điện thoại')
                                ->live(),

                            Toggle::make('show_seller_bank')
                                ->label('Hiển thị Số tài khoản ngân hàng')
                                ->live(),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('2. Tiêu Đề Hóa Đơn & Pháp Lý')
                    ->icon('heroicon-o-document-text')
                    ->description('Tiêu đề chính, số hóa đơn, ký hiệu mẫu và mã cơ quan thuế')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('invoice_title')
                                ->label('Tiêu đề Hóa đơn (Chữ in hoa)')
                                ->placeholder('Ví dụ: HÓA ĐƠN DỊCH VỤ & THANH TOÁN')
                                ->required()
                                ->live(onBlur: true),

                            TextInput::make('invoice_subtitle')
                                ->label('Phụ đề / Mẫu biểu căn cứ')
                                ->placeholder('Ví dụ: Makeup Artist & Beauty Studio hoặc Mẫu số 02 - VT')
                                ->live(onBlur: true),

                            TextInput::make('invoice_number_prefix')
                                ->label('Tiền tố Mã hóa đơn')
                                ->placeholder('Ví dụ: HD, BH, XK (để trống nếu không cần)')
                                ->live(onBlur: true),

                            Toggle::make('show_invoice_number')
                                ->label('Hiển thị Số hóa đơn (VD: #00012)')
                                ->live(),

                            Toggle::make('show_invoice_symbol')
                                ->label('Hiển thị Ký hiệu mẫu số (VD: 1C26TAV)')
                                ->live(),

                            TextInput::make('invoice_symbol')
                                ->label('Ký hiệu Hóa đơn')
                                ->placeholder('Ví dụ: 1C26TAV')
                                ->visible(fn ($get) => $get('show_invoice_symbol'))
                                ->live(onBlur: true),

                            Toggle::make('show_cqt_code')
                                ->label('Hiển thị Mã Cơ quan thuế (HĐĐT)')
                                ->live(),

                            TextInput::make('cqt_code')
                                ->label('Mã CQT mẫu')
                                ->placeholder('Ví dụ: 003447FDA6C1054E3CBBBB4A4C5AE4D9FF')
                                ->visible(fn ($get) => $get('show_cqt_code'))
                                ->live(onBlur: true),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('3. Thông Tin Khách Hàng (Bên Mua)')
                    ->icon('heroicon-o-user')
                    ->description('Tùy chỉnh các trường thông tin của khách hàng được in trên phiếu')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('show_buyer_name')
                                ->label('Hiển thị Họ tên khách hàng')
                                ->live(),

                            Toggle::make('show_buyer_company')
                                ->label('Hiển thị Tên đơn vị / Công ty mua')
                                ->live(),

                            Toggle::make('show_buyer_tax')
                                ->label('Hiển thị Mã số thuế người mua')
                                ->live(),

                            Toggle::make('show_buyer_phone')
                                ->label('Hiển thị Số điện thoại / Zalo khách')
                                ->live(),

                            Toggle::make('show_buyer_address')
                                ->label('Hiển thị Địa chỉ khách hàng')
                                ->live(),

                            Toggle::make('show_payment_method')
                                ->label('Hiển thị Hình thức thanh toán (TM/CK)')
                                ->live(),

                            TextInput::make('payment_method_default')
                                ->label('Hình thức thanh toán mặc định')
                                ->placeholder('Ví dụ: Tiền mặt / Chuyển khoản')
                                ->visible(fn ($get) => $get('show_payment_method'))
                                ->live(onBlur: true),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('4. Bảng Kê Chi Tiết Dịch Vụ / Hàng Hóa')
                    ->icon('heroicon-o-table-cells')
                    ->description('Cấu hình các cột trong bảng danh sách dịch vụ và sản phẩm')
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('show_column_code')
                                ->label('Cột Mã hàng / Quy cách')
                                ->live(),

                            Toggle::make('show_column_unit')
                                ->label('Cột Đơn vị tính (ĐVT)')
                                ->live(),

                            Toggle::make('show_column_schedule')
                                ->label('Cột Lịch trình ngày giờ thực hiện')
                                ->live(),

                            Toggle::make('show_column_discount')
                                ->label('Cột Chiết khấu / Giảm giá')
                                ->live(),

                            Toggle::make('show_column_tax')
                                ->label('Cột Thuế suất GTGT (%)')
                                ->live(),

                            TextInput::make('tax_rate_default')
                                ->label('Thuế suất GTGT mặc định (%)')
                                ->numeric()
                                ->default(0)
                                ->visible(fn ($get) => $get('show_column_tax'))
                                ->live(onBlur: true),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('5. Tổng Kết Tiền, Thuế VAT & Đọc Số Tiền Bằng Chữ')
                    ->icon('heroicon-o-calculator')
                    ->description('Cấu hình bảng tổng hợp tiền hàng, thuế GTGT, tiền cọc và đọc số tiền bằng chữ')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('show_tax_summary')
                                ->label('Hiển thị Bảng tổng hợp các mức thuế VAT (8%, 10%)')
                                ->live(),

                            Toggle::make('show_deposit')
                                ->label('Hiển thị Tiền đặt cọc trước & Số tiền còn lại')
                                ->live(),

                            Toggle::make('show_debt')
                                ->label('Hiển thị Nợ cũ & Tổng công nợ (POS)')
                                ->live(),

                            Toggle::make('show_amount_in_words')
                                ->label('Tự động đọc số tiền bằng chữ tiếng Việt (Bắt buộc theo chuẩn kế toán)')
                                ->live(),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('6. Khung Thanh Toán VietQR Thông Minh')
                    ->icon('heroicon-o-qr-code')
                    ->description('Tích hợp mã VietQR Napas247 tự động điền số tiền và cú pháp chuyển khoản')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('show_vietqr')
                                ->label('Hiển thị Khung VietQR Chuyển Khoản')
                                ->helperText('Quét mã là tự động nạp chính xác STK + Số tiền + Cú pháp chuyển khoản trên mọi app ngân hàng')
                                ->live(),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('7. Ghi Chú, Lưu Ý & Lời Dặn Dò')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->description('Lời dặn dò khách hàng, chính sách đặt cọc/bảo hành & Lời cảm ơn chân trang')
                    ->schema([
                        Toggle::make('show_notes')
                            ->label('Hiển thị Khung Ghi chú & Lưu ý')
                            ->live(),

                        Textarea::make('notes_content')
                            ->label('Nội dung Ghi chú / Lưu ý / Điều khoản')
                            ->rows(3)
                            ->visible(fn ($get) => $get('show_notes'))
                            ->placeholder('Ví dụ: Quý khách vui lòng kiểm tra diện mạo trước khi rời studio...')
                            ->live(onBlur: true),

                        TextInput::make('footer_thank_you')
                            ->label('Lời cảm ơn chân trang')
                            ->placeholder('Ví dụ: Cảm ơn quý khách đã tin tưởng và lựa chọn dịch vụ của chúng tôi!')
                            ->live(onBlur: true),
                    ])
                    ->collapsible(),

                Section::make('8. Chân Trang Tra Cứu Hóa Đơn Trực Tuyến')
                    ->icon('heroicon-o-globe-alt')
                    ->description('Dành cho hóa đơn điện tử VAT cần link tra cứu và mã bảo mật')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('show_lookup_link')
                                ->label('Hiển thị Link tra cứu HĐĐT')
                                ->live(),

                            TextInput::make('lookup_url')
                                ->label('Đường link cổng tra cứu')
                                ->placeholder('Ví dụ: https://tracuu.hoadondientu.gdt.gov.vn')
                                ->visible(fn ($get) => $get('show_lookup_link'))
                                ->live(onBlur: true),
                        ]),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    /**
     * Nạp nhanh một Preset được chọn
     */
    public function applyPreset(string $presetKey): void
    {
        $presets = InvoiceConfigService::getPresets();
        if (!isset($presets[$presetKey])) {
            return;
        }

        $preset = $presets[$presetKey];
        $this->activePreset = $presetKey;
        
        $newConfig = $preset['config'];
        $newConfig['active_preset'] = $presetKey;

        $this->form->fill($newConfig);
        $this->data = $newConfig;

        Notification::make()
            ->title('Đã áp dụng mẫu ' . $preset['name'])
            ->body('Các thiết lập và công tắc đã được nạp tự động theo chuẩn của mẫu hóa đơn này.')
            ->success()
            ->send();
    }

    /**
     * Nhận sự kiện kéo thả sắp xếp lại thứ tự các khối
     */
    public function updateBlockOrder(array $newOrder): void
    {
        $this->data['block_order'] = $newOrder;
        
        Notification::make()
            ->title('Đã cập nhật vị trí các khối')
            ->body('Thứ tự hiển thị trên hóa đơn đã được sắp xếp lại.')
            ->info()
            ->send();
    }

    /**
     * Bật/Tắt một khối trực tiếp từ danh sách kéo thả
     */
    public function toggleBlock(string $blockId, bool $status): void
    {
        // Có thể map blockId sang trường dữ liệu tương ứng
        $map = [
            'seller_header' => 'show_seller_name',
            'invoice_meta' => 'show_invoice_number',
            'buyer_info' => 'show_buyer_name',
            'items_table' => 'show_column_unit',
            'summary_totals' => 'show_amount_in_words',
            'vietqr_banking' => 'show_vietqr',
            'notes_terms' => 'show_notes',
            'footer_lookup' => 'show_lookup_link',
        ];

        if (isset($map[$blockId])) {
            $field = $map[$blockId];
            $this->data[$field] = $status;
        }
    }

    /**
     * Lưu toàn bộ cấu hình vào database
     */
    public function save(): void
    {
        $state = $this->form->getState();
        $state['active_preset'] = $this->activePreset;
        
        // Đảm bảo mảng block_order được lưu lại đầy đủ
        if (empty($state['block_order'])) {
            $state['block_order'] = $this->data['block_order'] ?? InvoiceConfigService::getCurrentConfig()['block_order'];
        }

        InvoiceConfigService::saveConfig($state);
        Cache::forget('settings_all');

        Notification::make()
            ->title('Lưu cấu hình hóa đơn thành công!')
            ->body('Hệ thống in ấn và xuất file hóa đơn đã được cập nhật theo mẫu mới nhất.')
            ->success()
            ->send();
    }

    /**
     * Khôi phục mẫu mặc định
     */
    public function resetToDefault(): void
    {
        $this->applyPreset('luxury_service');
        $this->save();

        Notification::make()
            ->title('Đã khôi phục cài đặt gốc')
            ->body('Cấu hình hóa đơn đã được đưa về mẫu chuẩn Luxury Studio ban đầu.')
            ->warning()
            ->send();
    }

    /**
     * Lấy dữ liệu cấu hình để truyền sang Live Preview Blade
     */
    public function getPreviewConfigProperty(): array
    {
        return array_merge(InvoiceConfigService::getCurrentConfig(), $this->data ?? []);
    }

    /**
     * Lấy danh sách các khối theo thứ tự đã sắp xếp
     */
    public function getOrderedBlocksProperty(): array
    {
        $available = InvoiceConfigService::getAvailableBlocks();
        $order = $this->data['block_order'] ?? InvoiceConfigService::getCurrentConfig()['block_order'] ?? array_keys($available);

        $ordered = [];
        foreach ($order as $blockId) {
            if (isset($available[$blockId])) {
                $ordered[$blockId] = $available[$blockId];
            }
        }

        // Thêm các khối chưa có trong order vào cuối
        foreach ($available as $blockId => $block) {
            if (!isset($ordered[$blockId])) {
                $ordered[$blockId] = $block;
            }
        }

        return $ordered;
    }
}
