<x-filament-panels::page>
    <style>
        /* BỐ CỤC 2 CỘT: CỘT TRÁI (CẤU HÌNH) RỘNG 60% - CỘT PHẢI (PREVIEW) 40% */
        .custom-builder-layout {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .custom-builder-layout {
                flex-direction: row;
                align-items: flex-start;
            }
            .config-column-left {
                flex: 1.35;
                min-width: 520px;
            }
            .preview-column-right {
                flex: 0.95;
                min-width: 380px;
                max-width: 480px;
                position: sticky;
                top: 20px;
            }
        }

        /* CARD PRESETS CHUẨN DARK / LIGHT */
        .preset-card-item {
            background-color: #ffffff;
            color: #1f2937;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dark .preset-card-item {
            background-color: #1f2937;
            color: #f3f4f6;
            border-color: #374151;
        }
        .preset-card-item:hover {
            border-color: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .preset-card-item.is-active {
            border-color: #f59e0b;
            background-color: #fffbeb;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
        }
        .dark .preset-card-item.is-active {
            background-color: #451a03;
            border-color: #f59e0b;
        }

        /* KHỐI VISUAL TRÊN TỜ HÓA ĐƠN */
        .visual-element-box {
            position: relative;
            border: 1.5px dashed transparent;
            border-radius: 8px;
            padding: 6px 8px;
            margin-bottom: 8px;
            transition: all 0.15s ease;
        }
        .visual-element-box:hover {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.03);
        }
        .visual-element-box.is-selected {
            border-color: #2563eb;
            border-style: solid;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
            background-color: rgba(37, 99, 235, 0.04);
        }

        /* TOOLBAR ĐIỀU KHIỂN TRỰC TIẾP TRÊN KHỐI (CANVA / WORDPRESS STYLE) */
        .element-action-toolbar {
            position: absolute;
            top: -16px;
            left: 8px;
            display: none;
            align-items: center;
            gap: 3px;
            background: #0f172a;
            color: white;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 600;
            z-index: 40;
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
            user-select: none;
        }
        .visual-element-box:hover .element-action-toolbar,
        .visual-element-box.is-selected .element-action-toolbar {
            display: flex;
        }

        .tb-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 5px;
            border-radius: 4px;
            background: #334155;
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.1s ease;
            line-height: 1;
        }
        .tb-btn:hover {
            background: #2563eb;
            color: white;
        }
        .tb-btn.active {
            background: #f59e0b;
            color: black;
            font-weight: bold;
        }
        .tb-btn.drag-handle {
            cursor: grab;
            background: #1e293b;
            color: #94a3b8;
        }
        .tb-btn.drag-handle:hover {
            background: #2563eb;
            color: white;
        }
        .tb-btn.drag-handle:active {
            cursor: grabbing;
        }

        .sortable-ghost {
            opacity: 0.35;
            background: #dbeafe !important;
            border: 2px dashed #2563eb !important;
        }
    </style>

    <div class="space-y-6">
        
        {{-- 1. THANH CHỌN PRESET NHANH (SỬA GIAO DIỆN TƯƠNG THÍCH DARK/LIGHT 100%) --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-sparkles" class="w-5 h-5 text-amber-500" />
                        Chọn Nhanh Mẫu Hóa Đơn Định Sẵn (Quick Presets)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Bấm vào một mẫu để hệ thống tự động nạp cấu hình và định dạng chuẩn.
                    </p>
                </div>
                <button type="button" wire:click="resetToDefault" wire:confirm="Khôi phục cấu hình về mẫu Luxury mặc định?" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 dark:text-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 rounded-lg transition">
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
                    <div wire:click="applyPreset('{{ $key }}')" class="preset-card-item {{ $isActive ? 'is-active' : '' }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-amber-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                {{ $preset['badge'] }}
                            </span>
                            @if($isActive)
                                <span class="inline-flex items-center text-[11px] font-bold text-amber-600 dark:text-amber-400">
                                    <x-filament::icon icon="heroicon-s-check-circle" class="w-3.5 h-3.5 mr-0.5" /> Đang dùng
                                </span>
                            @endif
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white">{{ $preset['name'] }}</h4>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                            {{ $preset['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 2. BỐ CỤC 2 CỘT CHUẨN: TRÁI 60% (CẤU HÌNH) - PHẢI 40% (CANVAS PREVIEW) --}}
        <div class="custom-builder-layout">
            
            {{-- CỘT TRÁI: FORM CẤU HÌNH RỘNG RÃI --}}
            <div class="config-column-left space-y-4">
                <form wire:submit="save" class="space-y-4">
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Bảng Cấu Hình & Tùy Biến Chi Tiết</h4>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400">Điều chỉnh các ô nhập liệu, kích thước và công tắc bật/tắt.</p>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow transition transform hover:-translate-y-0.5">
                            <x-filament::icon icon="heroicon-o-check" class="w-4 h-4" />
                            Lưu Cấu Hình
                        </button>
                    </div>

                    {{ $this->form }}

                    <div class="p-2">
                        <button type="submit" class="w-full py-3.5 text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                            💾 Lưu Toàn Bộ Cấu Hình & Bố Cục Hóa Đơn
                        </button>
                    </div>
                </form>
            </div>

            {{-- CỘT PHẢI: KHUNG PREVIEW CANVAS THỜI GIAN THỰC KÈM ĐIỀU KHIỂN TRỰC TIẾP --}}
            <div class="preview-column-right space-y-3">
                
                {{-- HEADER CANVAS --}}
                <div class="bg-gray-800 text-white p-3 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="flex h-2.5 w-2.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider">Khung Xem Trước (Canvas)</span>
                    </div>
                    <button type="button" onclick="printInvoicePreview()" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold text-gray-900 bg-white hover:bg-gray-100 rounded-lg shadow transition">
                        <x-filament::icon icon="heroicon-o-printer" class="w-3 h-3 text-gray-700" /> In Thử
                    </button>
                </div>

                @php
                    $preview = $this->previewConfig;
                    $elementsLayout = $preview['elements_layout'] ?? [];
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

                    $themeHeaderBg = match($preview['color_theme'] ?? 'luxury') {
                        'luxury' => 'text-[#c8a98d]',
                        'corporate' => 'text-blue-700',
                        'classic' => 'text-gray-800',
                        default => 'text-black',
                    };
                @endphp

                {{-- TỜ HÓA ĐƠN TRẮNG CHUẨN --}}
                <div class="bg-gray-200 dark:bg-gray-950 p-3 sm:p-4 rounded-2xl shadow-inner flex justify-center">
                    <div id="visual-invoice-sheet" class="bg-white text-gray-900 border border-gray-300 rounded-xl p-5 shadow-xl w-full font-sans transition-all text-xs" style="color: #222; max-width: 440px;">
                        
                        @foreach($this->orderedBlocks as $blockId => $block)
                            @php
                                $bLayout = $elementsLayout[$blockId] ?? ['align' => 'left', 'width' => 'full', 'size' => 'md'];
                                $alignClass = match($bLayout['align'] ?? 'left') {
                                    'center' => 'text-center items-center justify-center',
                                    'right' => 'text-right items-end justify-end',
                                    'between' => 'flex justify-between items-start',
                                    default => 'text-left items-start justify-start',
                                };
                                $widthClass = match($bLayout['width'] ?? 'full') {
                                    'half' => 'w-full',
                                    default => 'w-full',
                                };
                                $sizeClass = match($bLayout['size'] ?? 'md') {
                                    'sm' => 'text-[10.5px] scale-95 origin-top',
                                    'lg' => 'text-sm scale-105 origin-top',
                                    default => 'text-xs',
                                };
                            @endphp

                            <div data-block-id="{{ $blockId }}" class="visual-element-box {{ $widthClass }} {{ $sizeClass }}" id="box-{{ $blockId }}">
                                
                                {{-- CANVA / ELEMENTOR HOVER TOOLBAR --}}
                                <div class="element-action-toolbar">
                                    <span class="tb-btn drag-handle" title="Kéo lên/xuống để đổi vị trí">
                                        <x-filament::icon icon="heroicon-o-bars-3" class="w-3 h-3 mr-0.5 inline" /> Kéo
                                    </span>
                                    
                                    {{-- CĂN LỀ TRÁI / GIỮA / PHẢI --}}
                                    <span class="tb-btn {{ ($bLayout['align'] ?? 'left') === 'left' ? 'active' : '' }}" wire:click="setElementAlign('{{ $blockId }}', 'left')" title="Căn trái">⬅</span>
                                    <span class="tb-btn {{ ($bLayout['align'] ?? 'left') === 'center' ? 'active' : '' }}" wire:click="setElementAlign('{{ $blockId }}', 'center')" title="Căn giữa">⏺</span>
                                    <span class="tb-btn {{ ($bLayout['align'] ?? 'left') === 'right' ? 'active' : '' }}" wire:click="setElementAlign('{{ $blockId }}', 'right')" title="Căn phải">➡</span>

                                    {{-- THU NHỎ / PHÓNG TO --}}
                                    <span class="tb-btn {{ ($bLayout['size'] ?? 'md') === 'sm' ? 'active' : '' }}" wire:click="setElementSize('{{ $blockId }}', 'sm')" title="Cỡ chữ nhỏ">➖</span>
                                    <span class="tb-btn {{ ($bLayout['size'] ?? 'md') === 'md' ? 'active' : '' }}" wire:click="setElementSize('{{ $blockId }}', 'md')" title="Cỡ chữ vừa">▪</span>
                                    <span class="tb-btn {{ ($bLayout['size'] ?? 'md') === 'lg' ? 'active' : '' }}" wire:click="setElementSize('{{ $blockId }}', 'lg')" title="Cỡ chữ lớn">➕</span>
                                </div>

                                {{-- 1. SELLER HEADER --}}
                                @if($blockId === 'seller_header')
                                    <div class="pb-2 border-b border-gray-200 {{ $alignClass }}">
                                        <div class="space-y-0.5">
                                            @if($preview['show_seller_name'])
                                                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wide text-gray-900">
                                                    {{ $settings['site_name'] ?? 'THẢO MAKEUP STUDIO & ACADEMY' }}
                                                </h2>
                                            @endif
                                            @if($preview['show_seller_tax'])
                                                <p class="text-[10px] text-gray-600"><strong>MST:</strong> {{ $settings['business_tax_code'] ?? '0110076629' }}</p>
                                            @endif
                                            @if($preview['show_seller_address'])
                                                <p class="text-[10px] text-gray-600 leading-tight"><strong>Đ/C:</strong> {{ $settings['address_main'] ?? ($settings['address'] ?? '102 Vũ Phạm Hàm, Cầu Giấy, Hà Nội') }}</p>
                                            @endif
                                            @if($preview['show_seller_phone'])
                                                <p class="text-[10px] text-gray-600"><strong>Hotline:</strong> {{ $settings['hotline'] ?? '0912.345.678' }}</p>
                                            @endif
                                            @if($preview['show_seller_bank'])
                                                <p class="text-[10px] text-gray-600"><strong>STK:</strong> {{ $settings['bank_account_number'] ?? '19032344013012' }} ({{ $settings['bank_name'] ?? 'Techcombank' }})</p>
                                            @endif
                                        </div>
                                        @if($preview['show_logo'])
                                            <div class="flex-shrink-0 ml-2">
                                                @if(!empty($settings['site_logo']))
                                                    <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="max-h-9 object-contain">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[9px] border border-amber-300">
                                                        LOGO
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                {{-- 2. INVOICE META --}}
                                @elseif($blockId === 'invoice_meta')
                                    <div class="py-1.5 {{ $alignClass }}">
                                        <h1 class="text-sm sm:text-base font-bold uppercase tracking-wider {{ $themeHeaderBg }}">
                                            {{ $preview['invoice_title'] ?: 'HÓA ĐƠN DỊCH VỤ' }}
                                        </h1>
                                        @if(!empty($preview['invoice_subtitle']))
                                            <p class="text-[10px] text-gray-500 italic mt-0.5">{{ $preview['invoice_subtitle'] }}</p>
                                        @endif
                                        <div class="flex flex-wrap items-center justify-center gap-2 text-[10px] text-gray-500 mt-1">
                                            <span>Ngày: {{ date('d/m/Y') }}</span>
                                            @if($preview['show_invoice_number'])
                                                <span>• <strong>Số:</strong> {{ $preview['invoice_number_prefix'] }}#00068</span>
                                            @endif
                                            @if($preview['show_invoice_symbol'] && !empty($preview['invoice_symbol']))
                                                <span>• <strong>Ký hiệu:</strong> {{ $preview['invoice_symbol'] }}</span>
                                            @endif
                                        </div>
                                    </div>

                                {{-- 3. BUYER INFO --}}
                                @elseif($blockId === 'buyer_info')
                                    <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-100 text-[11px] space-y-0.5 {{ $alignClass }}">
                                        @if($preview['show_buyer_name'])
                                            <p><strong>Khách hàng:</strong> Nguyễn Hoàng Mai</p>
                                        @endif
                                        @if($preview['show_buyer_phone'])
                                            <p><strong>Điện thoại:</strong> 0988.776.655</p>
                                        @endif
                                        @if($preview['show_buyer_company'])
                                            <p><strong>Đơn vị:</strong> Công ty TNHH Sen Vàng</p>
                                        @endif
                                        @if($preview['show_buyer_address'])
                                            <p><strong>Địa chỉ:</strong> Ngoại Giao Đoàn, Hà Nội</p>
                                        @endif
                                        @if($preview['show_payment_method'])
                                            <p><strong>Thanh toán:</strong> {{ $preview['payment_method_default'] ?: 'Tiền mặt / Chuyển khoản' }}</p>
                                        @endif
                                    </div>

                                {{-- 4. ITEMS TABLE --}}
                                @elseif($blockId === 'items_table')
                                    <div class="overflow-x-auto py-1">
                                        <table class="w-full text-left border-collapse text-[11px]">
                                            <thead>
                                                <tr class="bg-gray-100 border-y border-gray-300 font-bold text-gray-700">
                                                    <th class="py-1 px-1.5 text-center w-6">#</th>
                                                    <th class="py-1 px-1.5">Dịch Vụ / Sản Phẩm</th>
                                                    @if($preview['show_column_unit'])
                                                        <th class="py-1 px-1 text-center w-8">ĐVT</th>
                                                    @endif
                                                    <th class="py-1 px-1 text-center w-6">SL</th>
                                                    <th class="py-1 px-1.5 text-right w-16">Đơn Giá</th>
                                                    <th class="py-1 px-1.5 text-right w-18">T.Tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                <tr>
                                                    <td class="py-1 px-1.5 text-center text-gray-500">1</td>
                                                    <td class="py-1 px-1.5">
                                                        <strong>Makeup Cô Dâu VIP</strong>
                                                        @if($preview['show_column_schedule'])
                                                            <div class="text-[9px] text-gray-500">📅 06:30 28/10/2026</div>
                                                        @endif
                                                    </td>
                                                    @if($preview['show_column_unit'])
                                                        <td class="py-1 px-1 text-center text-gray-500">Gói</td>
                                                    @endif
                                                    <td class="py-1 px-1 text-center">1</td>
                                                    <td class="py-1 px-1.5 text-right">3.500k</td>
                                                    <td class="py-1 px-1.5 text-right font-bold">3.500k</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-1 px-1.5 text-center text-gray-500">2</td>
                                                    <td class="py-1 px-1.5">
                                                        <strong>Makeup Mẹ Cô Dâu</strong>
                                                        @if($preview['show_column_schedule'])
                                                            <div class="text-[9px] text-gray-500">📅 07:30 28/10/2026</div>
                                                        @endif
                                                    </td>
                                                    @if($preview['show_column_unit'])
                                                        <td class="py-1 px-1 text-center text-gray-500">Người</td>
                                                    @endif
                                                    <td class="py-1 px-1 text-center">1</td>
                                                    <td class="py-1 px-1.5 text-right">1.000k</td>
                                                    <td class="py-1 px-1.5 text-right font-bold">1.000k</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                {{-- 5. SUMMARY TOTALS --}}
                                @elseif($blockId === 'summary_totals')
                                    <div class="border-t border-gray-300 pt-2 text-[11px] space-y-1 {{ $alignClass }}">
                                        <div class="flex justify-between font-bold text-gray-900 border-b border-gray-200 pb-1">
                                            <span>Tổng cộng:</span>
                                            <span class="text-amber-700 font-extrabold">{{ number_format($demoGrandTotal) }} đ</span>
                                        </div>
                                        @if($preview['show_deposit'])
                                            <div class="flex justify-between text-gray-600 text-[10px]">
                                                <span>Đã đặt cọc:</span>
                                                <span class="text-emerald-600 font-semibold">- {{ number_format($demoDeposit) }} đ</span>
                                            </div>
                                            <div class="flex justify-between text-[11px] font-bold text-red-600">
                                                <span>Còn lại cần thu:</span>
                                                <span>{{ number_format($demoRemaining) }} đ</span>
                                            </div>
                                        @endif
                                        @if($preview['show_amount_in_words'])
                                            <div class="pt-1 text-[10px] text-gray-600 italic">
                                                <strong>Bằng chữ:</strong> {{ \App\Helpers\InvoiceHelper::docTienBangChu($preview['show_deposit'] ? $demoRemaining : $demoGrandTotal) }}
                                            </div>
                                        @endif
                                    </div>

                                {{-- 6. VIETQR BANKING --}}
                                @elseif($blockId === 'vietqr_banking' && $preview['show_vietqr'])
                                    <div class="p-2.5 rounded-lg border border-dashed border-amber-300 bg-amber-50/50 flex items-center justify-between gap-2 text-[10.5px]">
                                        <div class="space-y-0.5">
                                            <div class="font-bold text-amber-900 uppercase text-[10px]">VietQR Chuyển Khoản</div>
                                            <p class="text-gray-600">{{ $settings['bank_name'] ?? 'Techcombank' }}: <strong class="text-amber-700 font-mono">{{ $settings['bank_account_number'] ?? '19032344013012' }}</strong></p>
                                            <p class="text-gray-600">CTK: <strong>{{ $settings['bank_account_holder'] ?? 'NGUYEN THI THAO' }}</strong></p>
                                            <p class="text-[9px] text-gray-500">Cú pháp: <strong>HD 00068 0988776655</strong></p>
                                        </div>
                                        @if($qrUrl)
                                            <img src="{{ $qrUrl }}" alt="VietQR" class="w-16 h-16 object-contain rounded border border-white shadow-sm flex-shrink-0">
                                        @endif
                                    </div>

                                {{-- 7. NOTES & TERMS --}}
                                @elseif($blockId === 'notes_terms')
                                    @if($preview['show_notes'] && !empty($preview['notes_content']))
                                        <div class="p-2 bg-gray-50 rounded text-[10px] text-gray-600 border border-gray-100 whitespace-pre-line leading-relaxed {{ $alignClass }}">
                                            <strong>Lưu ý:</strong> {{ $preview['notes_content'] }}
                                        </div>
                                    @endif
                                    @if(!empty($preview['footer_thank_you']))
                                        <div class="text-center my-1 text-[10.5px] italic text-gray-500">
                                            {{ $preview['footer_thank_you'] }}
                                        </div>
                                    @endif

                                {{-- 8. SIGNATURES --}}
                                @elseif($blockId === 'signatures')
                                    @php $sigType = $preview['signature_type'] ?? 'two_parties'; @endphp
                                    @if($sigType === 'two_parties')
                                        <div class="my-3 grid grid-cols-2 text-center text-[10px]">
                                            <div>
                                                <div class="font-bold uppercase text-gray-700">KHÁCH HÀNG</div>
                                                <div class="text-[9px] text-gray-400 italic">(Ký, họ tên)</div>
                                                <div class="h-10"></div>
                                                <div class="font-semibold text-gray-600">Nguyễn Hoàng Mai</div>
                                            </div>
                                            <div>
                                                <div class="font-bold uppercase text-gray-700">NGƯỜI LẬP PHIẾU</div>
                                                <div class="text-[9px] text-gray-400 italic">(Ký, họ tên)</div>
                                                <div class="h-10"></div>
                                                <div class="font-semibold text-gray-600">{{ $settings['site_name'] ?? 'Thảo Makeup' }}</div>
                                            </div>
                                        </div>
                                    @elseif($sigType === 'digital_stamp')
                                        <div class="my-3 text-right">
                                            <div class="border-2 border-emerald-500 rounded p-2 text-left bg-emerald-50/50 text-[9px] text-emerald-800 shadow-sm inline-block">
                                                <div class="font-bold text-emerald-600 flex items-center gap-0.5 mb-0.5">
                                                    <x-filament::icon icon="heroicon-s-check-badge" class="w-3 h-3 text-emerald-600" /> Signature Valid
                                                </div>
                                                <div>Ký bởi: <strong>{{ $settings['site_name'] ?? 'THẢO MAKEUP STUDIO' }}</strong></div>
                                                <div>Ký ngày: {{ date('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                    @endif

                                {{-- 9. FOOTER LOOKUP --}}
                                @elseif($blockId === 'footer_lookup' && $preview['show_lookup_link'])
                                    <div class="pt-2 border-t border-gray-200 text-center text-[9px] text-gray-500 {{ $alignClass }}">
                                        <p>Tra cứu tại: <a href="{{ $preview['lookup_url'] }}" target="_blank" class="text-blue-600 underline">{{ $preview['lookup_url'] }}</a> - Mã: <strong>8BFLCX5VBP8J</strong></p>
                                    </div>
                                @endif

                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SORTABLEJS --}}
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
                    handle: '.drag-handle',
                    draggable: '.visual-element-box',
                    onEnd: function () {
                        const items = Array.from(sheet.querySelectorAll('.visual-element-box'));
                        const newOrder = items.map(el => el.getAttribute('data-block-id'));
                        @this.updateBlockOrder(newOrder);
                    }
                });
            }
        }

        function printInvoicePreview() {
            const container = document.getElementById('visual-invoice-sheet');
            const clone = container.cloneNode(true);
            
            clone.querySelectorAll('.element-action-toolbar').forEach(el => el.remove());
            clone.querySelectorAll('.visual-element-box').forEach(el => {
                el.style.border = 'none';
                el.style.padding = '0';
                el.style.margin = '0 0 8px 0';
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
