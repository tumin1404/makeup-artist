<x-filament-panels::page>
    <style>
        /* 2-COLUMN BUILDER WORKSPACE */
        .builder-workspace {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            align-items: flex-start;
        }
        @media (min-width: 1024px) {
            .builder-workspace {
                flex-direction: row;
                align-items: flex-start;
            }
        }

        .builder-sidebar-panel {
            width: 100%;
        }
        @media (min-width: 1024px) {
            .builder-sidebar-panel {
                width: 440px;
                min-width: 420px;
                max-width: 460px;
                position: sticky;
                top: 20px;
                max-height: calc(100vh - 40px);
                overflow-y: auto;
            }
        }

        .builder-canvas-panel {
            flex: 1;
            width: 100%;
            min-width: 0;
            background: #e5e7eb;
            border-radius: 20px;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);
        }
        .dark .builder-canvas-panel {
            background: #111827;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.3);
        }

        /* ELEMENTOR / WORDPRESS VISUAL BLOCK WRAPPER */
        .visual-block-item {
            position: relative;
            transition: all 0.2s ease;
            border: 1.5px dashed transparent;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .visual-block-item:hover {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.02);
        }

        .visual-block-item.is-selected {
            border-color: #2563eb;
            border-style: solid;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
            background-color: rgba(37, 99, 235, 0.03);
        }

        /* ELEMENTOR HOVER TOOLBAR */
        .block-toolbar {
            position: absolute;
            top: -14px;
            left: 12px;
            display: none;
            align-items: center;
            gap: 4px;
            background: #1e293b;
            color: white;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            z-index: 30;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            user-select: none;
        }

        .visual-block-item:hover .block-toolbar,
        .visual-block-item.is-selected .block-toolbar {
            display: flex;
        }

        .block-drag-handle {
            cursor: grab;
            display: inline-flex;
            align-items: center;
            padding: 2px 4px;
            background: #334155;
            border-radius: 4px;
            color: #94a3b8;
        }
        .block-drag-handle:hover {
            color: white;
            background: #2563eb;
        }
        .block-drag-handle:active {
            cursor: grabbing;
        }

        .sortable-ghost {
            opacity: 0.4;
            background: #dbeafe !important;
            border: 2px dashed #2563eb !important;
        }
    </style>

    <div class="space-y-6">
        {{-- THANH PRESETS NHANH --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-sparkles" class="w-5 h-5 text-amber-500" />
                        Chọn Nhanh Mẫu Hóa Đơn Định Sẵn (Presets)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Nạp mẫu nhanh với 1 cú click để tự động điền cấu hình và bố cục phù hợp.
                    </p>
                </div>
                <button type="button" wire:click="resetToDefault" wire:confirm="Khôi phục cấu hình về mẫu Luxury mặc định?" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-800 rounded-lg transition">
                    <x-filament::icon icon="heroicon-o-arrow-path" class="w-3.5 h-3.5" />
                    Khôi phục gốc
                </button>
            </div>

            @php
                $presets = \App\Services\InvoiceConfigService::getPresets();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($presets as $key => $preset)
                    @php $isActive = ($activePreset === $key); @endphp
                    <div wire:click="applyPreset('{{ $key }}')" class="cursor-pointer p-3 rounded-xl border-2 transition-all flex flex-col justify-between {{ $isActive ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-950/20 shadow-md' : 'border-gray-200 dark:border-gray-800 hover:border-gray-300 bg-white dark:bg-gray-800/40' }}">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                    {{ $preset['badge'] }}
                                </span>
                                @if($isActive)
                                    <span class="inline-flex items-center text-[11px] font-bold text-amber-600 dark:text-amber-400">
                                        <x-filament::icon icon="heroicon-s-check-circle" class="w-4 h-4 mr-0.5" /> Đang dùng
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white">{{ $preset['name'] }}</h4>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $preset['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 2-COLUMN WORKSPACE: LEFT SIDEBAR + RIGHT INTERACTIVE CANVAS --}}
        <div class="builder-workspace">
            
            {{-- CỘT TRÁI: FORM CÀI ĐẶT & INSPECTOR --}}
            <div class="builder-sidebar-panel space-y-4">
                <form wire:submit="save" class="space-y-4">
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Bảng Cấu Hình Chi Tiết</h4>
                            <p class="text-[11px] text-gray-500">Tùy biến các trường nội dung và công tắc.</p>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow transition transform hover:-translate-y-0.5">
                            <x-filament::icon icon="heroicon-o-check" class="w-4 h-4" />
                            Lưu Cấu Hình
                        </button>
                    </div>

                    {{ $this->form }}

                    <div class="p-3 text-center">
                        <button type="submit" class="w-full py-3 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-lg transition">
                            💾 Lưu Toàn Bộ Cấu Hình Hóa Đơn
                        </button>
                    </div>
                </form>
            </div>

            {{-- CỘT PHẢI: KHUNG CANVAS KÉO THẢ TRỰC TIẾP TRÊN TỜ HÓA ĐƠN --}}
            <div class="builder-canvas-panel">
                
                {{-- TOOLBAR CANVAS --}}
                <div class="w-full max-w-[700px] mb-4 flex flex-wrap items-center justify-between gap-3 bg-white/90 dark:bg-gray-800/90 backdrop-blur px-4 py-2.5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                            <x-filament::icon icon="heroicon-o-cursor-arrow-rays" class="w-3.5 h-3.5" />
                            Kéo thả trực tiếp bên dưới (WordPress Builder Mode)
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="printInvoicePreview()" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-gray-800 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-white rounded-lg transition">
                            <x-filament::icon icon="heroicon-o-printer" class="w-3.5 h-3.5" /> In Thử
                        </button>
                    </div>
                </div>

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
                        'k80' => 'max-w-[360px] text-[11px]',
                        default => 'max-w-[680px] text-xs',
                    };

                    $themeHeaderBg = match($preview['color_theme'] ?? 'luxury') {
                        'luxury' => 'text-[#c8a98d]',
                        'corporate' => 'text-blue-700',
                        'classic' => 'text-gray-800',
                        default => 'text-black',
                    };
                @endphp

                {{-- TỜ HÓA ĐƠN PREVIEW CANVAS CHỨA CÁC KHỐI KÉO THẢ TRỰC TIẾP --}}
                <div id="visual-invoice-sheet" class="bg-white text-gray-900 border border-gray-300 rounded-2xl p-6 sm:p-10 shadow-2xl w-full {{ $paperClass }} font-sans relative transition-all" style="color: #222; min-height: 800px;">
                    
                    @foreach($this->orderedBlocks as $blockId => $block)
                        
                        <div data-block-id="{{ $blockId }}" class="visual-block-item" id="canvas-block-{{ $blockId }}">
                            
                            {{-- WORDPRESS/ELEMENTOR HOVER TOOLBAR --}}
                            <div class="block-toolbar">
                                <span class="block-drag-handle" title="Kéo giữ để di chuyển khối này">
                                    <x-filament::icon icon="heroicon-o-bars-3" class="w-3.5 h-3.5 mr-1 inline" /> Kéo
                                </span>
                                <span class="px-1 text-[10px] text-amber-300 font-semibold">{{ $block['name'] }}</span>
                            </div>

                            {{-- 1. SELLER HEADER --}}
                            @if($blockId === 'seller_header')
                                <div class="pb-3 border-b border-gray-200">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            @if($preview['show_seller_name'])
                                                <h2 class="text-base sm:text-lg font-bold uppercase tracking-wide text-gray-900">
                                                    {{ $settings['site_name'] ?? 'THẢO MAKEUP STUDIO & ACADEMY' }}
                                                </h2>
                                            @endif
                                            @if($preview['show_seller_tax'])
                                                <p class="text-[11px] text-gray-600"><strong>MST:</strong> {{ $settings['business_tax_code'] ?? '0110076629' }}</p>
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
                                <div class="text-center py-2">
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
                                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-100 text-xs space-y-1">
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
                                <div class="overflow-x-auto py-1">
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
                                <div class="border-t border-gray-300 pt-3 text-xs space-y-1.5">
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
                                <div class="p-3.5 rounded-xl border border-dashed border-amber-300 bg-amber-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
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
                                    <div class="p-3 bg-gray-50 rounded-lg text-[11px] text-gray-600 border border-gray-100 whitespace-pre-line leading-relaxed">
                                        <strong>Ghi chú & Dặn dò:</strong><br>
                                        {{ $preview['notes_content'] }}
                                    </div>
                                @endif

                                @if(!empty($preview['footer_thank_you']))
                                    <div class="text-center my-2 text-xs italic text-gray-500">
                                        {{ $preview['footer_thank_you'] }}
                                    </div>
                                @endif

                            {{-- 8. SIGNATURES --}}
                            @elseif($blockId === 'signatures')
                                @php $sigType = $preview['signature_type'] ?? 'two_parties'; @endphp

                                @if($sigType === 'two_parties')
                                    <div class="my-4 grid grid-cols-2 text-center text-xs">
                                        <div>
                                            <div class="font-bold uppercase text-gray-700">KHÁCH HÀNG</div>
                                            <div class="text-[10px] text-gray-400 italic mt-0.5">(Ký, ghi rõ họ tên)</div>
                                            <div class="h-14"></div>
                                            <div class="font-semibold text-gray-600">Nguyễn Hoàng Mai</div>
                                        </div>
                                        <div>
                                            <div class="font-bold uppercase text-gray-700">NGƯỜI LẬP PHIẾU</div>
                                            <div class="text-[10px] text-gray-400 italic mt-0.5">(Ký, ghi rõ họ tên)</div>
                                            <div class="h-14"></div>
                                            <div class="font-semibold text-gray-600">{{ $settings['site_name'] ?? 'Thảo Makeup' }}</div>
                                        </div>
                                    </div>

                                @elseif($sigType === 'digital_stamp')
                                    <div class="my-4 grid grid-cols-2 text-center text-xs items-end">
                                        <div>
                                            <div class="font-bold uppercase text-gray-700">Người mua hàng</div>
                                            <div class="text-[10px] text-gray-400 italic mt-0.5">(Chữ ký số nếu có)</div>
                                            <div class="h-14"></div>
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
                                    <div class="my-4 grid grid-cols-5 text-center text-[10px] gap-1">
                                        <div>
                                            <div class="font-bold uppercase">Người lập</div>
                                            <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                            <div class="h-10"></div>
                                        </div>
                                        <div>
                                            <div class="font-bold uppercase">Người nhận</div>
                                            <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                            <div class="h-10"></div>
                                        </div>
                                        <div>
                                            <div class="font-bold uppercase">Thủ kho</div>
                                            <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                            <div class="h-10"></div>
                                        </div>
                                        <div>
                                            <div class="font-bold uppercase">Kế toán</div>
                                            <div class="text-gray-400 italic">(Ký, họ tên)</div>
                                            <div class="h-10"></div>
                                        </div>
                                        <div>
                                            <div class="font-bold uppercase">Giám đốc</div>
                                            <div class="text-gray-400 italic">(Đóng dấu)</div>
                                            <div class="h-10"></div>
                                        </div>
                                    </div>
                                @endif

                            {{-- 9. FOOTER LOOKUP --}}
                            @elseif($blockId === 'footer_lookup' && $preview['show_lookup_link'])
                                <div class="pt-3 border-t border-gray-200 text-center text-[10px] text-gray-500 space-y-0.5">
                                    <p>Tra cứu hóa đơn tại Website: <a href="{{ $preview['lookup_url'] }}" target="_blank" class="text-blue-600 underline font-medium">{{ $preview['lookup_url'] }}</a> - Mã tra cứu: <strong>8BFLCX5VBP8J</strong></p>
                                    <p class="text-gray-400 font-light">(Cần kiểm tra, đối chiếu khi lập, giao, nhận hóa đơn)</p>
                                </div>
                            @endif

                        </div>

                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- SORTABLEJS TÍCH HỢP TRỰC TIẾP TRÊN TỜ HÓA ĐƠN PREVIEW CANVAS (WORDPRESS / ELEMENTOR STYLE) --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initVisualCanvasSortable();
        });

        document.addEventListener('livewire:navigated', function () {
            initVisualCanvasSortable();
        });

        function initVisualCanvasSortable() {
            const sheet = document.getElementById('visual-invoice-sheet');
            if (sheet && !sheet.dataset.sortableInitialized) {
                sheet.dataset.sortableInitialized = 'true';
                new Sortable(sheet, {
                    animation: 250,
                    ghostClass: 'sortable-ghost',
                    handle: '.block-drag-handle',
                    draggable: '.visual-block-item',
                    onEnd: function () {
                        const items = Array.from(sheet.querySelectorAll('.visual-block-item'));
                        const newOrder = items.map(el => el.getAttribute('data-block-id'));
                        @this.updateBlockOrder(newOrder);
                    }
                });
            }
        }

        function printInvoicePreview() {
            const container = document.getElementById('visual-invoice-sheet');
            const clone = container.cloneNode(true);
            
            // Xóa các hover toolbar khi in
            clone.querySelectorAll('.block-toolbar').forEach(el => el.remove());
            clone.querySelectorAll('.visual-block-item').forEach(el => {
                el.style.border = 'none';
                el.style.padding = '0';
                el.style.margin = '0 0 10px 0';
            });

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
                    ${clone.outerHTML}
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
