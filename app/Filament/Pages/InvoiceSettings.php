<?php

namespace App\Filament\Pages;

use App\Helpers\InvoiceHelper;
use App\Models\Setting;
use App\Services\InvoiceConfigService;
use Filament\Forms\Components\Fieldset;
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
    public array $gridLayout = [];

    public function mount(): void
    {
        $config = InvoiceConfigService::getCurrentConfig();
        $this->activePreset = $config['active_preset'] ?? 'luxury_service';
        $this->gridLayout = $config['grid_layout'] ?? InvoiceConfigService::getDefaultGridLayout();
        $this->form->fill($config);
        $this->data = $config;
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
                    ->description('Bật/tắt các trường thông tin của Studio / Doanh nghiệp in trên hóa đơn')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('show_logo')
                                ->label('Hiển thị Logo Studio')
                                ->helperText('Sử dụng logo cấu hình từ cài đặt chung')
                                ->live(),

                            Toggle::make('show_seller_name')
                                ->label('Hiển thị Tên Studio / Công ty')
                                ->live(),

                            Toggle::make('show_seller_phone')
                                ->label('Hiển thị Hotline / Số điện thoại')
                                ->live(),

                            Toggle::make('show_seller_address')
                                ->label('Hiển thị Địa chỉ cơ sở')
                                ->live(),

                            Toggle::make('show_seller_tax')
                                ->label('Hiển thị Mã số thuế bên bán (MST)')
                                ->live(),

                            Toggle::make('show_seller_bank')
                                ->label('Hiển thị Số tài khoản & Ngân hàng')
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
                        ]),

                        Grid::make(3)->schema([
                            Fieldset::make('Số Hóa Đơn')
                                ->columns(1)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('show_invoice_number')
                                        ->label('Hiển thị Số hóa đơn (#00012)')
                                        ->live(),

                                    TextInput::make('invoice_number_prefix')
                                        ->label('Tiền tố Mã hóa đơn')
                                        ->placeholder('Ví dụ: HD, BH, XK...')
                                        ->disabled(fn ($get) => ! $get('show_invoice_number'))
                                        ->live(onBlur: true),
                                ]),

                            Fieldset::make('Ký Hiệu Mẫu Số')
                                ->columns(1)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('show_invoice_symbol')
                                        ->label('Hiển thị Ký hiệu mẫu số (VD: 1C26TAV)')
                                        ->live(),

                                    TextInput::make('invoice_symbol')
                                        ->label('Ký hiệu Hóa đơn')
                                        ->placeholder('Ví dụ: 1C26TAV')
                                        ->disabled(fn ($get) => ! $get('show_invoice_symbol'))
                                        ->live(onBlur: true),
                                ]),

                            Fieldset::make('Mã Cơ Quan Thuế (HĐĐT)')
                                ->columns(1)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('show_cqt_code')
                                        ->label('Hiển thị Mã Cơ quan thuế')
                                        ->live(),

                                    TextInput::make('cqt_code')
                                        ->label('Mã CQT mẫu')
                                        ->placeholder('Ví dụ: 003447FDA6C1054E3CBBBB...')
                                        ->disabled(fn ($get) => ! $get('show_cqt_code'))
                                        ->live(onBlur: true),
                                ]),
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

                            Toggle::make('show_buyer_phone')
                                ->label('Hiển thị Số điện thoại / Zalo khách')
                                ->live(),

                            Toggle::make('show_buyer_address')
                                ->label('Hiển thị Địa chỉ khách hàng')
                                ->live(),

                            Toggle::make('show_buyer_company')
                                ->label('Hiển thị Tên đơn vị / Công ty mua')
                                ->live(),

                            Toggle::make('show_buyer_tax')
                                ->label('Hiển thị Mã số thuế người mua')
                                ->live(),

                            Fieldset::make('Hình Thức Thanh Toán')
                                ->columns(1)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('show_payment_method')
                                        ->label('Hiển thị Hình thức thanh toán (TM/CK)')
                                        ->live(),

                                    TextInput::make('payment_method_default')
                                        ->label('Hình thức thanh toán mặc định')
                                        ->placeholder('Ví dụ: Tiền mặt / Chuyển khoản')
                                        ->disabled(fn ($get) => ! $get('show_payment_method'))
                                        ->live(onBlur: true),
                                ]),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('4. Bảng Kê Chi Tiết Dịch Vụ / Hàng Hóa')
                    ->icon('heroicon-o-table-cells')
                    ->description('Cấu hình các cột trong bảng danh sách dịch vụ và sản phẩm')
                    ->schema([
                        Grid::make(2)->schema([
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

                            Fieldset::make('Thuế Suất Giá Trị Gia Tăng (VAT)')
                                ->columns(1)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('show_column_tax')
                                        ->label('Hiển thị Cột Thuế suất GTGT (%)')
                                        ->live(),

                                    TextInput::make('tax_rate_default')
                                        ->label('Thuế suất GTGT mặc định (%)')
                                        ->numeric()
                                        ->default(0)
                                        ->placeholder('Ví dụ: 8 hoặc 10')
                                        ->disabled(fn ($get) => ! $get('show_column_tax'))
                                        ->live(onBlur: true),
                                ]),
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
                                ->label('Tự động đọc số tiền bằng chữ tiếng Việt (Chuẩn kế toán)')
                                ->live(),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('6. Khung Thanh Toán VietQR Thông Minh')
                    ->icon('heroicon-o-qr-code')
                    ->description('Tích hợp mã VietQR Napas247 tự động điền số tiền và cú pháp chuyển khoản')
                    ->schema([
                        Toggle::make('show_vietqr')
                            ->label('Hiển thị Khung VietQR Chuyển Khoản')
                            ->helperText('Quét mã là tự động nạp chính xác STK + Số tiền + Cú pháp chuyển khoản trên mọi app ngân hàng')
                            ->live(),
                    ])
                    ->collapsible(),

                Section::make('7. Ghi Chú, Lưu Ý & Lời Dặn Dò')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->description('Lời dặn dò khách hàng, chính sách đặt cọc/bảo hành & Lời cảm ơn chân trang')
                    ->schema([
                        Fieldset::make('Khung Ghi Chú & Lưu Ý')
                            ->columns(1)
                            ->schema([
                                Toggle::make('show_notes')
                                    ->label('Hiển thị Khung Ghi chú & Lưu ý trên hóa đơn')
                                    ->live(),

                                Textarea::make('notes_content')
                                    ->label('Nội dung Ghi chú / Lưu ý / Điều khoản')
                                    ->rows(3)
                                    ->disabled(fn ($get) => ! $get('show_notes'))
                                    ->placeholder('Ví dụ: Quý khách vui lòng kiểm tra diện mạo trước khi rời studio...')
                                    ->live(onBlur: true),
                            ]),

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
                            Fieldset::make('Tra Cứu Hóa Đơn Điện Tử')
                                ->columns(1)
                                ->columnSpan(1)
                                ->schema([
                                    Toggle::make('show_lookup_link')
                                        ->label('Hiển thị Link tra cứu HĐĐT')
                                        ->live(),

                                    TextInput::make('lookup_url')
                                        ->label('Đường link cổng tra cứu')
                                        ->placeholder('Ví dụ: https://tracuu.hoadondientu.gdt.gov.vn')
                                        ->disabled(fn ($get) => ! $get('show_lookup_link'))
                                        ->live(onBlur: true),
                                ]),
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
        $this->gridLayout = $newConfig['grid_layout'] ?? InvoiceConfigService::getDefaultGridLayout();

        $this->form->fill($newConfig);
        $this->data = $newConfig;
        $this->data['grid_layout'] = $this->gridLayout;

        Notification::make()
            ->title('Đã nạp mẫu: ' . $preset['name'])
            ->body('Cấu hình và vị trí mẫu đã được nạp vào khung xem trước để bạn chỉnh sửa. Lưu ý: Mẫu chưa được lưu vào hệ thống, hãy nhấn "💾 Lưu Toàn Bộ Cấu Hình" bên dưới để hoàn tất.')
            ->info()
            ->duration(6000)
            ->send();
    }

    /**
     * Cập nhật toàn bộ Layout Grid 2D từ sự kiện kéo thả
     */
    public function updateGridLayout(array $newLayout): void
    {
        $this->gridLayout = $newLayout;
        $this->data['grid_layout'] = $newLayout;
        
        $this->ensureBlocksStructure();
        foreach ($newLayout as $item) {
            $bId = $item['id'] ?? '';
            if ($bId && isset($this->data['blocks_structure'][$bId])) {
                $this->data['blocks_structure'][$bId]['width'] = (($item['w'] ?? 12) >= 12) ? 'full' : 'half';
            }
        }
    }

    /**
     * Cập nhật độ rộng (Số cột 1-12) của widget từ thao tác kéo viền trực tiếp
     */
    public function updateWidgetSpan(string $blockId, int $newSpan): void
    {
        $this->ensureBlocksStructure();
        $found = false;
        foreach ($this->gridLayout as &$item) {
            if (($item['id'] ?? '') === $blockId) {
                $item['w'] = $newSpan;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $this->gridLayout[] = [
                'id' => $blockId,
                'x' => 0,
                'y' => 0,
                'w' => $newSpan,
                'h' => 3
            ];
        }

        $this->data['grid_layout'] = $this->gridLayout;
        if (isset($this->data['blocks_structure'][$blockId])) {
            $this->data['blocks_structure'][$blockId]['width'] = ($newSpan >= 12 ? 'full' : 'half');
        }
    }

    /**
     * Cập nhật thứ tự các Phần tử con (Elements) bên trong một Khối cụ thể
     */
    public function updateElementOrderInBlock(string $blockId, array $newElementOrder): void
    {
        $this->ensureBlocksStructure();

        if (isset($this->data['blocks_structure'][$blockId])) {
            $this->data['blocks_structure'][$blockId]['elements_order'] = $newElementOrder;
        }
    }

    /**
     * Di chuyển một phần tử con LÊN TRÊN trong khối
     */
    public function moveElementUp(string $blockId, string $elementId): void
    {
        $this->ensureBlocksStructure();
        $order = $this->data['blocks_structure'][$blockId]['elements_order'] ?? [];
        $index = array_search($elementId, $order);
        
        if ($index !== false && $index > 0) {
            $temp = $order[$index - 1];
            $order[$index - 1] = $order[$index];
            $order[$index] = $temp;
            $this->data['blocks_structure'][$blockId]['elements_order'] = array_values($order);
        }
    }

    /**
     * Di chuyển một phần tử con XUỐNG DƯỚI trong khối
     */
    public function moveElementDown(string $blockId, string $elementId): void
    {
        $this->ensureBlocksStructure();
        $order = $this->data['blocks_structure'][$blockId]['elements_order'] ?? [];
        $index = array_search($elementId, $order);
        
        if ($index !== false && $index < count($order) - 1) {
            $temp = $order[$index + 1];
            $order[$index + 1] = $order[$index];
            $order[$index] = $temp;
            $this->data['blocks_structure'][$blockId]['elements_order'] = array_values($order);
        }
    }

    /**
     * Đổi căn lề của Khối (Trái / Giữa / Phải)
     */
    public function setBlockAlign(string $blockId, string $align): void
    {
        $this->ensureBlocksStructure();
        if (isset($this->data['blocks_structure'][$blockId])) {
            $this->data['blocks_structure'][$blockId]['align'] = $align;
        }
    }

    /**
     * Đổi căn lề Trái / Giữa / Phải của một phần tử con
     */
    public function setElementAlign(string $blockId, string $elementId, string $align): void
    {
        $this->ensureBlocksStructure();
        if (isset($this->data['blocks_structure'][$blockId])) {
            $this->data['blocks_structure'][$blockId]['align'] = $align;
        }
    }

    /**
     * Đổi kích cỡ To / Vừa / Nhỏ của một phần tử con
     */
    public function setElementSize(string $blockId, string $elementId, string $size): void
    {
        $this->ensureBlocksStructure();
        if (isset($this->data['blocks_structure'][$blockId])) {
            $this->data['blocks_structure'][$blockId]['size'] = $size;
        }
    }

    /**
     * Bật / Tắt hiển thị của một phần tử con
     */
    public function toggleElementVisibility(string $blockId, string $elementId): void
    {
        $this->ensureBlocksStructure();
        if (isset($this->data['blocks_structure'][$blockId])) {
            $curr = $this->data['blocks_structure'][$blockId]['visible'] ?? true;
            $this->data['blocks_structure'][$blockId]['visible'] = !$curr;
        }
    }

    /**
     * Đảm bảo state blocks_structure và grid_layout đã được khởi tạo
     */
    private function ensureBlocksStructure(): void
    {
        if (!isset($this->data['blocks_structure'])) {
            $this->data['blocks_structure'] = InvoiceConfigService::getCurrentConfig()['blocks_structure'] ?? InvoiceConfigService::getDefaultBlocksStructure();
        }
        if (empty($this->gridLayout)) {
            $this->gridLayout = $this->data['grid_layout'] ?? InvoiceConfigService::getCurrentConfig()['grid_layout'] ?? InvoiceConfigService::getDefaultGridLayout();
        }
    }

    /**
     * Lưu toàn bộ cấu hình vào database
     */
    public function save(): void
    {
        $state = $this->form->getState();
        $state['active_preset'] = $this->activePreset;
        
        $this->ensureBlocksStructure();
        $state['grid_layout'] = $this->gridLayout;
        $state['blocks_structure'] = $this->data['blocks_structure'];
        $state['blocks_order'] = array_column($this->gridLayout, 'id');

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
     * Lấy danh sách các Khối và Phần tử con sắp xếp theo tọa độ Grid (y, x)
     */
    public function getOrderedBlocksProperty(): array
    {
        $config = $this->previewConfig;
        $structure = $config['blocks_structure'] ?? InvoiceConfigService::getDefaultBlocksStructure();
        $gridLayout = $this->gridLayout ?: ($config['grid_layout'] ?? InvoiceConfigService::getDefaultGridLayout());

        // Sắp xếp gridLayout theo y tăng dần, sau đó x tăng dần
        usort($gridLayout, function ($a, $b) {
            $ay = $a['y'] ?? 0;
            $by = $b['y'] ?? 0;
            if ($ay === $by) {
                return ($a['x'] ?? 0) <=> ($b['x'] ?? 0);
            }
            return $ay <=> $by;
        });

        $ordered = [];
        foreach ($gridLayout as $item) {
            $bId = $item['id'] ?? '';
            if ($bId && isset($structure[$bId])) {
                $block = $structure[$bId];
                $block['grid_x'] = $item['x'] ?? 0;
                $block['grid_y'] = $item['y'] ?? 0;
                $block['grid_w'] = $item['w'] ?? 12;
                $block['grid_h'] = $item['h'] ?? 3;

                // Sắp xếp các phần tử con bên trong khối
                $elOrder = $block['elements_order'] ?? array_keys($block['elements'] ?? []);
                $orderedElements = [];
                foreach ($elOrder as $eId) {
                    if (isset($block['elements'][$eId])) {
                        $orderedElements[$eId] = $block['elements'][$eId];
                    }
                }
                foreach (($block['elements'] ?? []) as $eId => $eVal) {
                    if (!isset($orderedElements[$eId])) {
                        $orderedElements[$eId] = $eVal;
                    }
                }
                $block['ordered_elements'] = $orderedElements;
                $ordered[$bId] = $block;
            }
        }

        // Merge bất kỳ block nào còn thiếu
        foreach ($structure as $bId => $block) {
            if (!isset($ordered[$bId])) {
                $block['grid_x'] = 0;
                $block['grid_y'] = 99;
                $block['grid_w'] = 12;
                $block['grid_h'] = 3;
                $block['ordered_elements'] = $block['elements'] ?? [];
                $ordered[$bId] = $block;
            }
        }

        return $ordered;
    }
}
