<x-filament-panels::page>
    <div class="space-y-6">
        {{-- 1. THANH CHỌN PRESET NHANH (QUICK PRESET SELECTOR) --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-sparkles" class="w-5 h-5 text-amber-500" />
                        Chọn Mẫu Hóa Đơn Định Sẵn (Quick Presets)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Bấm chọn một mẫu để hệ thống tự động nạp cấu hình, bật/tắt các trường input và sắp xếp khối phù hợp.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="resetToDefault" wire:confirm="Bạn có chắc muốn khôi phục về mẫu Luxury mặc định ban đầu?" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 rounded-lg transition">
                        <x-filament::icon icon="heroicon-o-arrow-path" class="w-3.5 h-3.5" />
                        Khôi phục gốc
                    </button>
                </div>
            </div>

            @php
                $presets = \App\Services\InvoiceConfigService::getPresets();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach($presets as $key => $preset)
                    @php
                        $isActive = ($activePreset === $key);
                    @endphp
                    <div wire:click="applyPreset('{{ $key }}')" class="cursor-pointer relative p-4 rounded-xl border-2 transition-all duration-200 flex flex-col justify-between {{ $isActive ? 'border-amber-500 bg-amber-50/40 dark:bg-amber-950/20 shadow-md transform -translate-y-0.5' : 'border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 bg-white dark:bg-gray-800/50' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    {{ $preset['badge'] }}
                                </span>
                                @if($isActive)
                                    <span class="flex h-2.5 w-2.5 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1.5">
                                {{ $preset['name'] }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                {{ $preset['description'] }}
                            </p>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs font-semibold {{ $isActive ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">
                            <span>{{ $isActive ? 'Đang kích hoạt' : 'Bấm để áp dụng' }}</span>
                            <x-filament::icon icon="{{ $isActive ? 'heroicon-s-check-circle' : 'heroicon-o-arrow-right' }}" class="w-4 h-4" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 2. GIAO DIỆN SPLIT-VIEW (BÊN TRÁI CẤU HÌNH & KÉO THẢ - BÊN PHẢI LIVE PREVIEW) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            {{-- CỘT TRÁI: DANH SÁCH KHỐI KÉO THẢ & FORM INPUTS (7 CỘT) --}}
            <div class="lg:col-span-6 space-y-6">
                
                {{-- KHUNG KÉO THẢ SẮP XẾP KHỐI (DRAG & DROP BLOCK BUILDER) --}}
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-filament::icon icon="heroicon-o-arrows-up-down" class="w-5 h-5 text-indigo-500" />
                                Kéo Thả Sắp Xếp Vị Trí Khối (Visual Layout)
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Kéo icon 3 gạch <strong>≡</strong> để đổi thứ tự xuất hiện trên hóa đơn.
                            </p>
                        </div>
                    </div>

                    <div id="invoice-blocks-list" class="space-y-2.5">
                        @foreach($this->orderedBlocks as $blockId => $block)
                            <div data-block-id="{{ $blockId }}" class="drag-block-item flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700/80 rounded-xl cursor-grab active:cursor-grabbing hover:border-indigo-400 dark:hover:border-indigo-500 transition-all shadow-sm">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-lg font-bold select-none cursor-grab">
                                        ≡
                                    </span>
                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                                        <x-filament::icon icon="{{ $block['icon'] }}" class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-gray-900 dark:text-white">{{ $block['name'] }}</div>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate max-w-[220px] sm:max-w-xs">{{ $block['description'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- FORM CHI TIẾT CÁC Ô INPUT CỦA FILAMENT --}}
                <form wire:submit="save" class="space-y-6">
                    {{ $this->form }}

                    <div class="sticky bottom-4 z-10 bg-white/90 dark:bg-gray-900/90 backdrop-blur border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-lg flex items-center justify-between gap-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Cấu hình áp dụng cho toàn bộ chức năng in hóa đơn.
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
                            <x-filament::icon icon="heroicon-o-check" class="w-4 h-4" />
                            Lưu Cấu Hình Hóa Đơn
                        </button>
                    </div>
                </form>
            </div>

            {{-- CỘT PHẢI: LIVE PREVIEW HÓA ĐƠN THEO THỜI GIAN THỰC (6 CỘT) --}}
            <div class="lg:col-span-6 lg:sticky lg:top-6 space-y-4">
                <div class="bg-gray-800 text-white rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-3 w-3 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider">Xem Trước Trực Tiếp (Live Preview)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="printInvoicePreview()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-gray-900 bg-white hover:bg-gray-100 rounded-lg shadow transition">
                            <x-filament::icon icon="heroicon-o-printer" class="w-3.5 h-3.5 text-gray-700" />
                            In Thử Mẫu Này
                        </button>
                    </div>
                </div>

                {{-- KHUNG HÓA ĐƠN PREVIEW --}}
                @php
                    $preview = $this->previewConfig;
                    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
                    $demoTotal = 4500000;
                    $demoDeposit = 1000000;
                    $demoRemaining = 3500000;
                    $demoTaxRate = $preview['tax_rate_default'] ?? 0;
                    $demoTaxAmount = $demoTotal * ($demoTaxRate / 100);
                    $demoGrandTotal = $demoTotal + $demoTaxAmount;
                    
                    $qrUrl = \App\Helpers\InvoiceHelper::generateVietQRUrl(
                        $settings['bank_name'] ?? 'MB Bank',
                        $settings['bank_account_number'] ?? '0912345678',
                        $settings['bank_account_holder'] ?? 'NGUYEN THAO MAKEUP',
                        $preview['show_deposit'] ? $demoRemaining : $demoGrandTotal,
                        'HD 00068 0988776655'
                    );

                    $paperClass = match($preview['paper_size'] ?? 'a4') {
                        'a5' => 'max-w-[580px] text-xs',
                        'k80' => 'max-w-[340px] text-[11px]',
                        default => 'max-w-[650px] text-xs',
                    };

                    $themeBorder = match($preview['color_theme'] ?? 'luxury') {
                        'luxury' => 'border-[#c8a98d] bg-[#fdfbf7] text-[#3e2f2f]',
                        'corporate' => 'border-blue-600 bg-white text-gray-900',
                        'classic' => 'border-gray-400 bg-white text-gray-900',
                        default => 'border-gray-800 bg-white text-black',
                    };

                    $themeHeaderBg = match($preview['color_theme'] ?? 'luxury') {
                        'luxury' => 'text-[#c8a98d]',
                        'corporate' => 'text-blue-700',
                        'classic' => 'text-gray-800',
                        default => 'text-black',
                    };
                @endphp

                <div id="invoice-preview-container" class="bg-white dark:bg-white text-gray-900 border border-gray-300 rounded-2xl p-6 sm:p-8 shadow-xl mx-auto {{ $paperClass }} font-sans transition-all overflow-hidden" style="color: #222;">
                    
                    {{-- LẶP QUA CÁC KHỐI THEO THỨ TỰ KÉO THẢ --}}
                    @foreach($this->orderedBlocks as $blockId => $block)
                        
                        {{-- 1. SELLER HEADER --}}
                        @if($blockId === 'seller_header')
                            <div class="pb-4 mb-4 border-b border-gray-200">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="space-y-1">
                                        @if($preview['show_seller_name'])
                                            <h2 class="text-base sm:text-lg font-bold uppercase tracking-wide text-gray-900" style="color: #1a1a1a;">
                                                {{ $settings['site_name'] ?? 'THẢO MAKEUP STUDIO & ACADEMY' }}
                                            </h2>
                                        @endif
                                        @if($preview['show_seller_tax'])
                                            <p class="text-[11px] text-gray-600"><strong>Mã số thuế:</strong> {{ $settings['business_tax_code'] ?? '0110076629' }}</p>
                                        @endif
                                        @if($preview['show_seller_address'])
                                            <p class="text-[11px] text-gray-600"><strong>Địa chỉ:</strong> {{ $settings['address_main'] ?? ($settings['address'] ?? '102 Vũ Phạm Hàm, Cầu Giấy, Hà Nội') }}</p>
                                        @endif
                                        @if($preview['show_seller_phone'])
                                            <p class="text-[11px] text-gray-600"><strong>Hotline:</strong> {{ $settings['hotline'] ?? '0912.345.678' }} {{ !empty($settings['email']) ? '| Email: ' . $settings['email'] : '' }}</p>
                                        @endif
                                        @if($preview['show_seller_bank'])
                                            <p class="text-[11px] text-gray-600"><strong>STK:</strong> {{ $settings['bank_account_number'] ?? '19032344013012' }} - {{ $settings['bank_name'] ?? 'Techcombank' }} ({{ $settings['bank_account_holder'] ?? 'NGUYỄN THỊ THẢO' }})</p>
                                        @endif
                                    </div>
                                    @if($preview['show_logo'])
                                        <div class="flex-shrink-0">
                                            @if(!empty($settings['site_logo']))
                                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="max-h-12 object-contain">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-xs border border-amber-300">
                                                    LOGO
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                        {{-- 2. INVOICE META --}}
                        @elseif($blockId === 'invoice_meta')
                            <div class="text-center my-4">
                                <h1 class="text-lg sm:text-xl font-bold uppercase tracking-wider {{ $themeHeaderBg }}">
                                    {{ $preview['invoice_title'] ?: 'HÓA ĐƠN DỊCH VỤ' }}
                                </h1>
                                @if(!empty($preview['invoice_subtitle']))
                                    <p class="text-xs text-gray-500 font-light mt-0.5 italic">{{ $preview['invoice_subtitle'] }}</p>
                                @endif
                                <div class="flex flex-wrap items-center justify-center gap-3 text-[11px] text-gray-500 mt-1.5">
                                    <span>Ngày: {{ date('d/m/Y') }}</span>
                                    @if($preview['show_invoice_number'])
                                        <span>• <strong>Số:</strong> {{ $preview['invoice_number_prefix'] }}#00068</span>
                                    @endif
                                    @if($preview['show_invoice_symbol'] && !empty($preview['invoice_symbol']))
                                        <span>• <strong>Ký hiệu:</strong> {{ $preview['invoice_symbol'] }}</span>
                                    @endif
                                </div>
                                @if($preview['show_cqt_code'] && !empty($preview['cqt_code']))
                                    <p class="text-[10px] text-gray-400 font-mono mt-1">Mã CQT: {{ $preview['cqt_code'] }}</p>
                                @endif
                            </div>

                        {{-- 3. BUYER INFO --}}
                        @elseif($blockId === 'buyer_info')
                            <div class="bg-gray-50 rounded-xl p-3.5 my-3 border border-gray-100 text-xs space-y-1">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                                    @if($preview['show_buyer_name'])
                                        <p><strong>Khách hàng:</strong> Nguyễn Hoàng Mai</p>
                                    @endif
                                    @if($preview['show_buyer_phone'])
                                        <p><strong>Điện thoại:</strong> 0988.776.655</p>
                                    @endif
                                    @if($preview['show_buyer_company'])
                                        <p><strong>Đơn vị/Cty:</strong> Công ty TNHH Truyền Thông Sen Vàng</p>
                                    @endif
                                    @if($preview['show_buyer_tax'])
                                        <p><strong>Mã số thuế:</strong> 0901130068</p>
                                    @endif
                                    @if($preview['show_buyer_address'])
                                        <p class="sm:col-span-2"><strong>Địa chỉ:</strong> Villa 12, KĐT Ngoại Giao Đoàn, Bắc Từ Liêm, Hà Nội</p>
                                    @endif
                                    @if($preview['show_payment_method'])
                                        <p class="sm:col-span-2"><strong>Hình thức thanh toán:</strong> {{ $preview['payment_method_default'] ?: 'Tiền mặt / Chuyển khoản' }}</p>
                                    @endif
                                </div>
                            </div>

                        {{-- 4. ITEMS TABLE --}}
                        @elseif($blockId === 'items_table')
                            <div class="my-4 overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-gray-100 border-y border-gray-300 font-bold text-gray-700">
                                            <th class="py-2 px-2 text-center w-8">STT</th>
                                            @if($preview['show_column_code'])
                                                <th class="py-2 px-2 w-16">Mã</th>
                                            @endif
                                            <th class="py-2 px-2">Tên Dịch Vụ / Hàng Hóa</th>
                                            @if($preview['show_column_unit'])
                                                <th class="py-2 px-2 text-center w-12">ĐVT</th>
                                            @endif
                                            <th class="py-2 px-2 text-center w-10">SL</th>
                                            <th class="py-2 px-2 text-right w-24">Đơn Giá</th>
                                            @if($preview['show_column_discount'])
                                                <th class="py-2 px-2 text-right w-16">CK</th>
                                            @endif
                                            @if($preview['show_column_tax'])
                                                <th class="py-2 px-2 text-center w-12">Thuế</th>
                                            @endif
                                            <th class="py-2 px-2 text-right w-24">Thành Tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr>
                                            <td class="py-2 px-2 text-center text-gray-500">1</td>
                                            @if($preview['show_column_code'])
                                                <td class="py-2 px-2 text-gray-500 font-mono">DV01</td>
                                            @endif
                                            <td class="py-2 px-2">
                                                <strong>Makeup Cô Dâu VIP Ngày Cưới</strong>
                                                @if($preview['show_column_schedule'])
                                                    <div class="text-[10px] text-gray-500 mt-0.5">📅 06:30 Ngày 28/10/2026 (Trang điểm tận nơi)</div>
                                                @endif
                                            </td>
                                            @if($preview['show_column_unit'])
                                                <td class="py-2 px-2 text-center text-gray-500">Gói</td>
                                            @endif
                                            <td class="py-2 px-2 text-center">1</td>
                                            <td class="py-2 px-2 text-right">3.500.000</td>
                                            @if($preview['show_column_discount'])
                                                <td class="py-2 px-2 text-right text-gray-500">0</td>
                                            @endif
                                            @if($preview['show_column_tax'])
                                                <td class="py-2 px-2 text-center text-gray-500">{{ $demoTaxRate }}%</td>
                                            @endif
                                            <td class="py-2 px-2 text-right font-bold">3.500.000</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 px-2 text-center text-gray-500">2</td>
                                            @if($preview['show_column_code'])
                                                <td class="py-2 px-2 text-gray-500 font-mono">DV02</td>
                                            @endif
                                            <td class="py-2 px-2">
                                                <strong>Makeup & Làm Tóc Mẹ Cô Dâu</strong>
                                                @if($preview['show_column_schedule'])
                                                    <div class="text-[10px] text-gray-500 mt-0.5">📅 07:30 Ngày 28/10/2026</div>
                                                @endif
                                            </td>
                                            @if($preview['show_column_unit'])
                                                <td class="py-2 px-2 text-center text-gray-500">Người</td>
                                            @endif
                                            <td class="py-2 px-2 text-center">1</td>
                                            <td class="py-2 px-2 text-right">1.000.000</td>
                                            @if($preview['show_column_discount'])
                                                <td class="py-2 px-2 text-right text-gray-500">0</td>
                                            @endif
                                            @if($preview['show_column_tax'])
                                                <td class="py-2 px-2 text-center text-gray-500">{{ $demoTaxRate }}%</td>
                                            @endif
                                            <td class="py-2 px-2 text-right font-bold">1.000.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        {{-- 5. SUMMARY TOTALS --}}
                        @elseif($blockId === 'summary_totals')
                            <div class="my-4 border-t border-gray-300 pt-3 text-xs space-y-1.5">
                                @if($preview['show_tax_summary'] && $demoTaxRate > 0)
                                    <div class="flex justify-between text-gray-600">
                                        <span>Cộng tiền hàng trước thuế:</span>
                                        <span>{{ number_format($demoTotal) }} đ</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Tiền thuế GTGT ({{ $demoTaxRate }}%):</span>
                                        <span>{{ number_format($demoTaxAmount) }} đ</span>
                                    </div>
                                @endif

                                <div class="flex justify-between text-sm font-bold text-gray-900 border-b border-gray-200 pb-1.5">
                                    <span>Tổng cộng thanh toán:</span>
                                    <span class="text-amber-700 font-extrabold text-base">{{ number_format($demoGrandTotal) }} VNĐ</span>
                                </div>

                                @if($preview['show_deposit'])
                                    <div class="flex justify-between text-gray-600">
                                        <span>Tiền đặt cọc trước (Đã nhận):</span>
                                        <span class="text-emerald-600 font-semibold">- {{ number_format($demoDeposit) }} đ</span>
                                    </div>
                                    <div class="flex justify-between text-xs font-bold text-red-600">
                                        <span>Số tiền còn lại cần thanh toán:</span>
                                        <span>{{ number_format($demoRemaining) }} đ</span>
                                    </div>
                                @endif

                                @if($preview['show_debt'])
                                    <div class="flex justify-between text-gray-500">
                                        <span>Nợ cũ:</span>
                                        <span>0 đ</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-gray-800">
                                        <span>Tổng nợ sau phiếu:</span>
                                        <span>{{ number_format($demoRemaining) }} đ</span>
                                    </div>
                                @endif

                                @if($preview['show_amount_in_words'])
                                    <div class="pt-2 text-[11px] text-gray-700 italic">
                                        <strong>Số tiền viết bằng chữ:</strong> {{ \App\Helpers\InvoiceHelper::docTienBangChu($preview['show_deposit'] ? $demoRemaining : $demoGrandTotal) }}
                                    </div>
                                @endif
                            </div>

                        {{-- 6. VIETQR BANKING --}}
                        @elseif($blockId === 'vietqr_banking' && $preview['show_vietqr'])
                            <div class="my-4 p-3.5 rounded-xl border border-dashed border-amber-300 bg-amber-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                                <div class="space-y-1">
                                    <div class="font-bold uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                                        <x-filament::icon icon="heroicon-o-qr-code" class="w-4 h-4 text-amber-600" />
                                        Quét Mã QR Chuyển Khoản Tức Thì (VietQR)
                                    </div>
                                    <p class="text-[11px] text-gray-600">Ngân hàng: <strong>{{ $settings['bank_name'] ?? 'Techcombank' }}</strong></p>
                                    <p class="text-[11px] text-gray-600">Số tài khoản: <strong class="text-amber-700 font-mono text-xs">{{ $settings['bank_account_number'] ?? '19032344013012' }}</strong></p>
                                    <p class="text-[11px] text-gray-600">Chủ tài khoản: <strong>{{ $settings['bank_account_holder'] ?? 'NGUYỄN THỊ THẢO' }}</strong></p>
                                    <p class="text-[10px] text-gray-500">Cú pháp: <strong>HD 00068 0988776655</strong></p>
                                </div>
                                <div class="flex-shrink-0 text-center">
                                    @if($qrUrl)
                                        <img src="{{ $qrUrl }}" alt="VietQR" class="w-24 h-24 object-contain rounded-lg border border-white shadow-sm">
                                    @endif
                                </div>
                            </div>

                        {{-- 7. NOTES & TERMS --}}
                        @elseif($blockId === 'notes_terms')
                            @if($preview['show_notes'] && !empty($preview['notes_content']))
                                <div class="my-3 p-3 bg-gray-50 rounded-lg text-[11px] text-gray-600 border border-gray-100 whitespace-pre-line leading-relaxed">
                                    <strong>Ghi chú & Dặn dò:</strong>
                                    {{ $preview['notes_content'] }}
                                </div>
                            @endif

                            @if(!empty($preview['footer_thank_you']))
                                <div class="text-center my-3 text-xs italic text-gray-500">
                                    {{ $preview['footer_thank_you'] }}
                                </div>
                            @endif

                        {{-- 8. SIGNATURES --}}
                        @elseif($blockId === 'signatures')
                            @php
                                $sigType = $preview['signature_type'] ?? 'two_parties';
                            @endphp

                            @if($sigType === 'two_parties')
                                <div class="my-6 grid grid-cols-2 text-center text-xs">
                                    <div>
                                        <div class="font-bold uppercase text-gray-700">KHÁCH HÀNG</div>
                                        <div class="text-[10px] text-gray-400 italic mt-0.5">(Ký, ghi rõ họ tên)</div>
                                        <div class="h-16"></div>
                                        <div class="font-semibold text-gray-600">Nguyễn Hoàng Mai</div>
                                    </div>
                                    <div>
                                        <div class="font-bold uppercase text-gray-700">NGƯỜI LẬP PHIẾU</div>
                                        <div class="text-[10px] text-gray-400 italic mt-0.5">(Ký, ghi rõ họ tên)</div>
                                        <div class="h-16"></div>
                                        <div class="font-semibold text-gray-600">{{ $settings['site_name'] ?? 'Thảo Makeup' }}</div>
                                    </div>
                                </div>

                            @elseif($sigType === 'digital_stamp')
                                <div class="my-6 grid grid-cols-2 text-center text-xs items-end">
                                    <div>
                                        <div class="font-bold uppercase text-gray-700">Người mua hàng</div>
                                        <div class="text-[10px] text-gray-400 italic mt-0.5">(Chữ ký số nếu có)</div>
                                        <div class="h-16"></div>
                                    </div>
                                    <div>
                                        <div class="font-bold uppercase text-gray-700 mb-1">Người bán hàng</div>
                                        <div class="border-2 border-emerald-500 rounded-lg p-2.5 text-left bg-emerald-50/50 text-[10px] text-emerald-800 shadow-sm inline-block w-full max-w-[220px]">
                                            <div class="font-bold text-emerald-600 flex items-center gap-1 mb-1">
                                                <x-filament::icon icon="heroicon-s-check-badge" class="w-3.5 h-3.5 text-emerald-600" />
                                                Signature Valid
                                            </div>
                                            <div>Ký bởi: <strong>{{ $settings['site_name'] ?? 'CÔNG TY TNHH THẢO MAKEUP' }}</strong></div>
                                            <div>Ký ngày: {{ date('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                </div>

                            @elseif($sigType === 'five_parties')
                                <div class="my-6 grid grid-cols-5 text-center text-[10px] gap-1">
                                    <div>
                                        <div class="font-bold uppercase">Người lập</div>
                                        <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                        <div class="h-12"></div>
                                    </div>
                                    <div>
                                        <div class="font-bold uppercase">Người nhận</div>
                                        <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                        <div class="h-12"></div>
                                    </div>
                                    <div>
                                        <div class="font-bold uppercase">Thủ kho</div>
                                        <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                        <div class="h-12"></div>
                                    </div>
                                    <div>
                                        <div class="font-bold uppercase">Kế toán</div>
                                        <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                        <div class="h-12"></div>
                                    </div>
                                    <div>
                                        <div class="font-bold uppercase">Giám đốc</div>
                                        <div class="text-gray-400 italic">(Đóng dấu)</div>
                                        <div class="h-12"></div>
                                    </div>
                                </div>
                            @endif

                        {{-- 9. FOOTER LOOKUP --}}
                        @elseif($blockId === 'footer_lookup' && $preview['show_lookup_link'])
                            <div class="my-4 pt-3 border-t border-gray-200 text-center text-[10px] text-gray-500 space-y-0.5">
                                <p>Tra cứu hóa đơn tại Website: <a href="{{ $preview['lookup_url'] }}" target="_blank" class="text-blue-600 underline font-medium">{{ $preview['lookup_url'] }}</a> - Mã tra cứu: <strong>8BFLCX5VBP8J</strong></p>
                                <p class="text-gray-400 font-light">(Cần kiểm tra, đối chiếu khi lập, giao, nhận hóa đơn)</p>
                            </div>
                        @endif

                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- SORTABLEJS TÍCH HỢP ĐỂ KÉO THẢ SẮP XẾP KHỐI --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('invoice-blocks-list');
            if (el) {
                new Sortable(el, {
                    animation: 150,
                    ghostClass: 'bg-indigo-100',
                    handle: '.drag-block-item',
                    onEnd: function () {
                        const order = Array.from(el.children).map(item => item.getAttribute('data-block-id'));
                        @this.updateBlockOrder(order);
                    }
                });
            }
        });

        function printInvoicePreview() {
            const container = document.getElementById('invoice-preview-container');
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>In Hóa Đơn</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        body { background: white; padding: 20px; font-family: sans-serif; }
                        @media print {
                            body { padding: 0; }
                            @page { margin: 10mm; }
                        }
                    </style>
                </head>
                <body>
                    ${container.outerHTML}
                    <script>
                        window.onload = function() { window.print(); }
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</x-filament-panels::page>
