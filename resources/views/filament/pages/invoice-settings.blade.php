<x-filament-panels::page>
    {{-- THƯ VIỆN GRIDSTACK.JS 2D GRID SYSTEM --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@10.3.1/dist/gridstack.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@10.3.1/dist/gridstack-extra.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/gridstack@10.3.1/dist/gridstack-all.js"></script>

    <style>
        /* KHUNG VIỀN NGOÀI CHỌN PRESET */
        .preset-container-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .dark .preset-container-box {
            background-color: #121215;
            border: 1px solid #27272a;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        /* CARD PRESETS: 2 HÀNG x 2 CỘT CÂN XỨNG */
        .preset-grid-2x2 {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 14px;
        }
        @media (min-width: 640px) {
            .preset-grid-2x2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .preset-card-item {
            background-color: #f8fafc;
            color: #0f172a;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .dark .preset-card-item {
            background-color: #18181b;
            color: #f4f4f5;
            border-color: #27272a;
        }
        .preset-card-item:hover {
            border-color: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        .dark .preset-card-item:hover {
            border-color: #f59e0b;
            box-shadow: 0 6px 18px rgba(0,0,0,0.4);
        }
        .preset-card-item.is-active {
            border-color: #f59e0b;
            border-width: 2px;
            background-color: #fffdf7;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
        }
        .dark .preset-card-item.is-active {
            background-color: #271c10;
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.35);
        }

        .preset-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 9999px;
            background-color: #e2e8f0;
            color: #334155;
        }
        .dark .preset-badge {
            background-color: #27272a;
            color: #d4d4d8;
        }
        .is-active .preset-badge {
            background-color: #f59e0b;
            color: #000000;
        }

        /* NÚT KHÔI PHỤC GỐC */
        .btn-reset-preset {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .btn-reset-preset:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
        .dark .btn-reset-preset {
            background-color: #27272a;
            color: #d4d4d8;
            border-color: #3f3f46;
        }
        .dark .btn-reset-preset:hover {
            background-color: #3f3f46;
            color: #ffffff;
        }

        /* KHUNG HEADER CẤU HÌNH */
        .config-header-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .dark .config-header-box {
            background-color: #121215;
            border-color: #27272a;
        }

        /* FIELDSET BOX STYLING GIỐNG PRESET CARD */
        fieldset.fi-fo-fieldset {
            background-color: #f8fafc !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 14px !important;
            padding: 14px 16px !important;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .dark fieldset.fi-fo-fieldset {
            background-color: #18181b !important;
            border-color: #27272a !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        fieldset.fi-fo-fieldset:hover {
            border-color: #cbd5e1 !important;
        }
        .dark fieldset.fi-fo-fieldset:hover {
            border-color: #3f3f46 !important;
        }
        fieldset.fi-fo-fieldset > legend {
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #475569 !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            background-color: #e2e8f0 !important;
        }
        .dark fieldset.fi-fo-fieldset > legend {
            color: #d4d4d8 !important;
            background-color: #27272a !important;
        }

        /* NÚT LƯU CẤU HÌNH */
        .btn-save-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 15px;
            font-size: 12px;
            font-weight: 700;
            color: #ffffff !important;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: 1px solid #d97706;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.35);
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-save-action:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.5);
            transform: translateY(-1px);
            color: #ffffff !important;
        }

        .btn-save-main {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 700;
            color: #ffffff !important;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border: 1px solid #d97706;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35);
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-save-main:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 6px 20px rgba(217, 119, 6, 0.5);
            transform: translateY(-1px);
            color: #ffffff !important;
        }

        /* HEADER CANVAS PREVIEW */
        .canvas-header-box {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            padding: 12px 18px;
            border-radius: 14px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .dark .canvas-header-box {
            background-color: #18181b;
            color: #f4f4f5;
            border-color: #27272a;
        }

        /* NÚT IN THỬ */
        .btn-print-preview {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #ffffff;
            color: #0f172a !important;
            border: 1px solid #cbd5e1;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .btn-print-preview:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }
        .dark .btn-print-preview {
            background-color: #27272a;
            color: #f4f4f5 !important;
            border-color: #3f3f46;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        .dark .btn-print-preview:hover {
            background-color: #3f3f46;
            border-color: #52525b;
            color: #ffffff !important;
        }

        /* ==================== GRIDSTACK 2D VISUAL BUILDER STYLES ==================== */
        .grid-stack {
            min-height: 480px;
            width: 100% !important;
        }
        
        .grid-stack-item {
            cursor: default;
        }

        .grid-stack-item-content {
            inset: 0 !important;
            overflow: visible !important;
        }

        .grid-block-card {
            background-color: #ffffff;
            border: 1.5px dashed #cbd5e1;
            border-radius: 10px;
            padding: 8px 10px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            position: relative;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .grid-block-card:hover {
            border-color: #f59e0b;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.18);
        }

        /* HEADER THANH CÔNG CỤ CỦA TỪNG KHỐI */
        .grid-block-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 6px;
            margin-bottom: 6px;
            user-select: none;
            font-size: 11px;
            gap: 4px;
        }
        .grid-block-card:hover .grid-block-header {
            background-color: #fffbeb;
            border-color: #fde68a;
        }

        .block-drag-handle {
            cursor: grab;
            display: flex;
            align-items: center;
            gap: 4px;
            color: #334155;
            font-weight: 700;
        }
        .block-drag-handle:active {
            cursor: grabbing;
        }

        .grid-block-quick-actions {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .grid-size-badge {
            font-size: 9.5px;
            font-weight: 800;
            font-family: ui-monospace, monospace;
            padding: 1.5px 4px;
            border-radius: 4px;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            line-height: 1.2;
        }

        .gb-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1.5px 4px;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 4px;
            background: #e2e8f0;
            color: #334155;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            line-height: 1.2;
        }
        .gb-btn:hover {
            background: #cbd5e1;
            color: #0f172a;
        }
        .gb-btn.active {
            background: #f59e0b;
            color: #000000;
            font-weight: 800;
        }

        .grid-block-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
        }

        /* PHẦN TỬ CON (ELEMENT) BÊN TRONG KHỐI */
        .draggable-element {
            position: relative;
            border: 1px dashed transparent;
            border-radius: 4px;
            padding: 2px 4px;
            margin: 1px 0;
            transition: all 0.15s ease;
        }
        .draggable-element:hover {
            border-color: #3b82f6;
            background-color: rgba(59, 130, 246, 0.05);
            z-index: 30;
        }

        .element-action-toolbar {
            position: absolute;
            top: -11px;
            left: 2px;
            display: none;
            align-items: center;
            gap: 2px;
            background: #0f172a;
            color: white;
            padding: 1.5px 5px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: 600;
            z-index: 50;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            user-select: none;
        }
        .draggable-element:hover > .element-action-toolbar {
            display: flex;
        }

        .tb-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1px 3px;
            border-radius: 3px;
            background: #334155;
            color: #e2e8f0;
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

        /* TÙY CHỈNH TAY CẦM RESIZE CỦA GRIDSTACK */
        .grid-stack > .grid-stack-item > .ui-resizable-handle {
            background-color: transparent;
        }
        .grid-stack > .grid-stack-item > .ui-resizable-e,
        .grid-stack > .grid-stack-item > .ui-resizable-w {
            width: 8px;
            top: 0;
            bottom: 0;
            cursor: col-resize;
        }
        .grid-stack > .grid-stack-item > .ui-resizable-s {
            height: 8px;
            left: 0;
            right: 0;
            cursor: row-resize;
        }
        .grid-stack > .grid-stack-item > .ui-resizable-se {
            width: 14px;
            height: 14px;
            right: 2px;
            bottom: 2px;
            cursor: se-resize;
            background: radial-gradient(circle, #f59e0b 2px, transparent 3px);
            background-size: 4px 4px;
        }
    </style>

    <div class="space-y-8">
        
        {{-- PHẦN 1: THANH CHỌN PRESET NHANH (LƯỚI 2x2 CÂN XỨNG) --}}
        <div class="preset-container-box">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-sparkles" class="w-5 h-5 text-amber-500" />
                        Chọn Nhanh Mẫu Hóa Đơn Định Sẵn (Presets)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Bấm vào một mẫu để hệ thống tự động nạp cấu hình và định dạng chuẩn.
                    </p>
                </div>
                <button type="button" wire:click="resetToDefault" wire:confirm="Khôi phục cấu hình về mẫu Luxury mặc định?" class="btn-reset-preset">
                    <x-filament::icon icon="heroicon-o-arrow-path" class="w-3.5 h-3.5" />
                    Khôi phục gốc
                </button>
            </div>

            @php
                $presets = \App\Services\InvoiceConfigService::getPresets();
            @endphp

            <div class="preset-grid-2x2">
                @foreach($presets as $key => $preset)
                    @php $isActive = ($activePreset === $key); @endphp
                    <div wire:click="applyPreset('{{ $key }}')" class="preset-card-item {{ $isActive ? 'is-active' : '' }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="preset-badge">
                                    {{ $preset['badge'] }}
                                </span>
                                @if($isActive)
                                    <span class="inline-flex items-center text-xs font-bold text-amber-500">
                                        <x-filament::icon icon="heroicon-s-check-circle" class="w-4 h-4 mr-1" /> Đang dùng
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">{{ $preset['name'] }}</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                                {{ $preset['description'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PHẦN 2: BẢNG FORM CẤU HÌNH & TÙY BIẾN CHI TIẾT (XẾP TRÊN) --}}
        <div class="space-y-4">
            <form wire:submit="save" class="space-y-4">
                <div class="config-header-box">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-adjustments-horizontal" class="w-5 h-5 text-amber-500" />
                            Bảng Cấu Hình & Tùy Biến Chi Tiết
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Điều chỉnh các trường dữ liệu, kích thước, thuế suất và công tắc bật/tắt.</p>
                    </div>
                </div>

                {{ $this->form }}

                <div class="p-2">
                    <button type="submit" class="btn-save-main">
                        💾 Lưu Toàn Bộ Cấu Hình & Bố Cục Hóa Đơn
                    </button>
                </div>
            </form>
        </div>

        {{-- PHẦN 3: KHUNG XEM TRƯỚC & TRÌNH KÉO THẢ GRIDSTACK 2D 12 CỘT (XẾP DƯỚI) --}}
        <div class="space-y-4">
            
            {{-- HEADER CANVAS --}}
            <div class="canvas-header-box">
                <div class="flex items-center gap-2">
                    <span class="flex h-3 w-3 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white flex items-center gap-2">
                            Khung Xem Trước & Trình Kéo Thả 2D Gridstack (12 Cột)
                        </h3>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                            ✨ Kéo thả tự do 2 chiều (Lên/Xuống/Trái/Phải). Kéo cạnh góc để co giãn độ rộng (1/2, Full, 1/3...). Xếp 2 chữ ký hoặc 2 khối bất kỳ cùng 1 dòng cực kỳ dễ dàng!
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="save" class="btn-save-action">
                        <x-filament::icon icon="heroicon-o-check" class="w-3.5 h-3.5" /> Lưu Vị Trí
                    </button>
                    <button type="button" onclick="printInvoicePreview()" class="btn-print-preview">
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

                $themeHeaderBg = match($preview['color_theme'] ?? 'luxury') {
                    'luxury' => 'text-[#c8a98d]',
                    'corporate' => 'text-blue-700',
                    'classic' => 'text-gray-800',
                    default => 'text-black',
                };
            @endphp

            {{-- TỜ HÓA ĐƠN TRẮNG CHUẨN A4 VỚI LƯỚI GRIDSTACK 2D --}}
            <div class="bg-gray-100 dark:bg-gray-950 p-4 sm:p-8 rounded-2xl shadow-inner flex justify-center">
                <div id="visual-invoice-sheet" class="bg-white text-gray-900 border border-gray-300 rounded-xl p-6 sm:p-8 shadow-2xl w-full font-sans transition-all text-xs" style="color: #222; max-width: 840px;">
                    
                    {{-- CONTAINER LƯỚI GRIDSTACK 12 CỘT --}}
                    <div class="grid-stack" id="invoice-grid-canvas">
                        @foreach($this->orderedBlocks as $bId => $block)
                            @php
                                $itemX = $block['grid_x'] ?? 0;
                                $itemY = $block['grid_y'] ?? 0;
                                $itemW = $block['grid_w'] ?? 12;
                                $itemH = $block['grid_h'] ?? 3;
                                $bAlign = $block['align'] ?? 'left';
                                $bAlignClass = match($bAlign) {
                                    'right' => 'text-right items-end justify-end',
                                    'center' => 'text-center items-center justify-center',
                                    default => 'text-left items-start justify-start',
                                };

                                // Kiểm tra điều kiện hiển thị của khối
                                $isBlockVisible = true;
                                if ($bId === 'block_signature_customer' || $bId === 'block_signature_creator') {
                                    $isBlockVisible = (($preview['signature_type'] ?? 'two_parties') === 'two_parties');
                                } elseif ($bId === 'block_signatures') {
                                    $isBlockVisible = in_array($preview['signature_type'] ?? '', ['five_parties', 'digital_stamp']);
                                } elseif ($bId === 'block_vietqr') {
                                    $isBlockVisible = !empty($preview['show_vietqr']);
                                } elseif ($bId === 'block_notes') {
                                    $isBlockVisible = (!empty($preview['show_notes']) || !empty($preview['footer_thank_you']));
                                } elseif ($bId === 'block_lookup') {
                                    $isBlockVisible = !empty($preview['show_lookup_link']);
                                }
                            @endphp

                            @if($isBlockVisible)
                                <div class="grid-stack-item" 
                                     gs-id="{{ $bId }}" 
                                     gs-x="{{ $itemX }}" 
                                     gs-y="{{ $itemY }}" 
                                     gs-w="{{ $itemW }}" 
                                     gs-h="{{ $itemH }}"
                                     gs-min-w="3"
                                     id="gs-{{ $bId }}">
                                    
                                    <div class="grid-stack-item-content">
                                        <div class="grid-block-card">
                                            
                                            {{-- THANH HEADER CỦA KHỐI: DRAG HANDLE + NÚT 1/2, FULL, CĂN LỀ --}}
                                            <div class="grid-block-header">
                                                <span class="block-drag-handle" title="Nắm kéo để di chuyển tự do 2 chiều (Lên/Xuống/Trái/Phải)">
                                                    <x-filament::icon icon="heroicon-m-arrows-pointing-out" class="w-3.5 h-3.5 inline text-amber-500 mr-0.5" />
                                                    <span class="truncate max-w-[130px]">{{ $block['name'] }}</span>
                                                </span>
                                                <div class="grid-block-quick-actions">
                                                    <span class="grid-size-badge" title="Độ rộng cột hiện tại">{{ $itemW }}/12</span>
                                                    <button type="button" wire:click="setBlockWidth('{{ $bId }}', 6)" class="gb-btn {{ $itemW == 6 ? 'active' : '' }}" title="Nửa dòng (50%)">1/2</button>
                                                    <button type="button" wire:click="setBlockWidth('{{ $bId }}', 12)" class="gb-btn {{ $itemW == 12 ? 'active' : '' }}" title="Cả dòng (100%)">Full</button>
                                                    <button type="button" wire:click="setBlockWidth('{{ $bId }}', 4)" class="gb-btn {{ $itemW == 4 ? 'active' : '' }}" title="1/3 dòng (33%)">1/3</button>
                                                    <button type="button" wire:click="setBlockAlign('{{ $bId }}', 'left')" class="gb-btn {{ $bAlign === 'left' ? 'active' : '' }}" title="Căn trái">⬅</button>
                                                    <button type="button" wire:click="setBlockAlign('{{ $bId }}', 'center')" class="gb-btn {{ $bAlign === 'center' ? 'active' : '' }}" title="Căn giữa">⏺</button>
                                                    <button type="button" wire:click="setBlockAlign('{{ $bId }}', 'right')" class="gb-btn {{ $bAlign === 'right' ? 'active' : '' }}" title="Căn phải">➡</button>
                                                </div>
                                            </div>

                                            {{-- THÂN KHỐI CHỨA CÁC PHẦN TỬ CON --}}
                                            <div class="grid-block-body {{ $bAlignClass }}">
                                                @foreach($block['ordered_elements'] as $eId => $el)
                                                    @php
                                                        $eAlign = $el['align'] ?? $bAlign;
                                                        $eAlignClass = match($eAlign) {
                                                            'right' => 'text-right items-end justify-end',
                                                            'center' => 'text-center items-center justify-center',
                                                            default => 'text-left items-start justify-start',
                                                        };
                                                        $eSize = $el['size'] ?? 'md';
                                                        $eSizeClass = match($eSize) {
                                                            'sm' => 'text-[11px] scale-95 origin-left',
                                                            'lg' => 'text-sm font-bold scale-105 origin-left',
                                                            default => 'text-xs',
                                                        };
                                                        $isVisible = $el['visible'] ?? true;
                                                    @endphp

                                                    @if($isVisible)
                                                        <div data-element-id="{{ $eId }}" class="draggable-element {{ $eAlignClass }} {{ $eSizeClass }}" id="el-{{ $eId }}">
                                                            
                                                            {{-- TOOLBAR CẤP PHẦN TỬ CON --}}
                                                            <div class="element-action-toolbar">
                                                                <span class="tb-btn" wire:click="moveElementUp('{{ $bId }}', '{{ $eId }}')" title="Chuyển lên trên">🔼</span>
                                                                <span class="tb-btn" wire:click="moveElementDown('{{ $bId }}', '{{ $eId }}')" title="Chuyển xuống dưới">🔽</span>
                                                                <span class="tb-btn {{ $eAlign === 'left' ? 'active' : '' }}" wire:click="setElementAlign('{{ $bId }}', '{{ $eId }}', 'left')" title="Căn trái">⬅</span>
                                                                <span class="tb-btn {{ $eAlign === 'center' ? 'active' : '' }}" wire:click="setElementAlign('{{ $bId }}', '{{ $eId }}', 'center')" title="Căn giữa">⏺</span>
                                                                <span class="tb-btn {{ $eAlign === 'right' ? 'active' : '' }}" wire:click="setElementAlign('{{ $bId }}', '{{ $eId }}', 'right')" title="Căn phải">➡</span>
                                                                <span class="tb-btn {{ $eSize === 'sm' ? 'active' : '' }}" wire:click="setElementSize('{{ $bId }}', '{{ $eId }}', 'sm')" title="Chữ nhỏ">➖</span>
                                                                <span class="tb-btn {{ $eSize === 'lg' ? 'active' : '' }}" wire:click="setElementSize('{{ $bId }}', '{{ $eId }}', 'lg')" title="Chữ lớn">➕</span>
                                                            </div>

                                                            {{-- RENDER NỘI DUNG TỪNG PHẦN TỬ CON --}}

                                                            {{-- 1. KHỐI SELLER --}}
                                                            @if($eId === 'seller_logo' && $preview['show_logo'])
                                                                <div>
                                                                    @if(!empty($settings['site_logo']))
                                                                        <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo" class="max-h-12 object-contain inline-block">
                                                                    @else
                                                                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-xs border border-amber-300 inline-flex">LOGO</div>
                                                                    @endif
                                                                </div>
                                                            @elseif($eId === 'seller_name' && $preview['show_seller_name'])
                                                                <h2 class="text-sm sm:text-base font-bold uppercase tracking-wider text-gray-900">
                                                                    {{ $settings['site_name'] ?? 'THẢO MAKEUP STUDIO' }}
                                                                </h2>
                                                            @elseif($eId === 'seller_phone' && $preview['show_seller_phone'])
                                                                <p class="text-gray-600">Hotline: <strong class="text-gray-900">{{ $settings['hotline'] ?? '0912.345.678' }}</strong></p>
                                                            @elseif($eId === 'seller_address' && $preview['show_seller_address'])
                                                                <p class="text-gray-600">Địa chỉ: {{ $settings['address_main'] ?? ($settings['address'] ?? '102 Vũ Phạm Hàm, Cầu Giấy, Hà Nội') }}</p>
                                                            @elseif($eId === 'seller_tax' && $preview['show_seller_tax'])
                                                                <p class="text-gray-600">MST: <strong class="text-gray-900">{{ $settings['business_tax_code'] ?? '0110076629' }}</strong></p>
                                                            @elseif($eId === 'seller_bank' && $preview['show_seller_bank'])
                                                                <p class="text-gray-600">STK: <strong class="text-gray-900 font-mono">{{ $settings['bank_account_number'] ?? '19032344013012' }}</strong> ({{ $settings['bank_name'] ?? 'Techcombank' }})</p>

                                                            {{-- 2. KHỐI INVOICE META --}}
                                                            @elseif($eId === 'invoice_title')
                                                                <h1 class="text-base sm:text-lg font-bold uppercase tracking-wider {{ $themeHeaderBg }}">
                                                                    {{ $preview['invoice_title'] ?: 'HÓA ĐƠN DỊCH VỤ & THANH TOÁN' }}
                                                                </h1>
                                                            @elseif($eId === 'invoice_subtitle' && !empty($preview['invoice_subtitle']))
                                                                <p class="text-gray-500 italic text-[11px]">{{ $preview['invoice_subtitle'] }}</p>
                                                            @elseif($eId === 'invoice_number' && $preview['show_invoice_number'])
                                                                <p class="text-gray-600">Số hóa đơn: <strong class="text-amber-700 font-mono">{{ $preview['invoice_number_prefix'] }}#00068</strong></p>
                                                            @elseif($eId === 'invoice_date')
                                                                <p class="text-gray-500">Ngày lập: <strong>{{ date('d/m/Y') }}</strong></p>
                                                            @elseif($eId === 'invoice_symbol' && $preview['show_invoice_symbol'] && !empty($preview['invoice_symbol']))
                                                                <p class="text-gray-600">Ký hiệu: <strong>{{ $preview['invoice_symbol'] }}</strong></p>
                                                            @elseif($eId === 'invoice_cqt' && $preview['show_cqt_code'] && !empty($preview['cqt_code']))
                                                                <p class="text-gray-500 text-[10px] font-mono">Mã CQT: {{ $preview['cqt_code'] }}</p>

                                                            {{-- 3. KHỐI BUYER --}}
                                                            @elseif($eId === 'buyer_name' && $preview['show_buyer_name'])
                                                                <p><span class="text-gray-500">Khách hàng:</span> <strong class="text-gray-900 text-sm">Nguyễn Hoàng Mai</strong></p>
                                                            @elseif($eId === 'buyer_phone' && $preview['show_buyer_phone'])
                                                                <p><span class="text-gray-500">Số điện thoại / Zalo:</span> <strong class="text-gray-900 font-mono">0988.776.655</strong></p>
                                                            @elseif($eId === 'buyer_address' && $preview['show_buyer_address'])
                                                                <p><span class="text-gray-500">Địa chỉ thực hiện:</span> <span class="text-gray-800">Biệt thự Ngoại Giao Đoàn, Bắc Từ Liêm, Hà Nội</span></p>
                                                            @elseif($eId === 'buyer_company' && $preview['show_buyer_company'])
                                                                <p><span class="text-gray-500">Đơn vị / Công ty:</span> <strong class="text-gray-900">Công ty TNHH Sự Kiện Sen Vàng</strong></p>
                                                            @elseif($eId === 'buyer_tax' && $preview['show_buyer_tax'])
                                                                <p><span class="text-gray-500">Mã số thuế:</span> <strong class="text-gray-900 font-mono">0901130068</strong></p>
                                                            @elseif($eId === 'buyer_payment' && $preview['show_payment_method'])
                                                                <p><span class="text-gray-500">Hình thức thanh toán:</span> <span class="text-gray-800 font-medium">{{ $preview['payment_method_default'] ?: 'Tiền mặt / Chuyển khoản' }}</span></p>

                                                            {{-- 4. KHỐI ITEMS TABLE --}}
                                                            @elseif($eId === 'items_table_content')
                                                                <div class="overflow-x-auto py-1 my-1 w-full">
                                                                    <table class="w-full text-left border-collapse text-xs">
                                                                        <thead>
                                                                            <tr class="bg-gray-100 border-y border-gray-300 font-bold text-gray-700">
                                                                                <th class="py-2 px-2 text-center w-8">#</th>
                                                                                <th class="py-2 px-2">Dịch Vụ / Hàng Hóa</th>
                                                                                @if($preview['show_column_unit'])
                                                                                    <th class="py-2 px-2 text-center w-12">ĐVT</th>
                                                                                @endif
                                                                                <th class="py-2 px-2 text-center w-10">SL</th>
                                                                                <th class="py-2 px-2 text-right w-24">Đơn Giá</th>
                                                                                <th class="py-2 px-2 text-right w-28">Thành Tiền</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-gray-200">
                                                                            <tr>
                                                                                <td class="py-2 px-2 text-center text-gray-500">1</td>
                                                                                <td class="py-2 px-2">
                                                                                    <strong class="text-gray-900">Makeup Cô Dâu VIP Ngày Cưới</strong>
                                                                                    @if($preview['show_column_schedule'])
                                                                                        <div class="text-[10px] text-gray-500">📅 Lịch hẹn: 06:30 Ngày 28/10/2026</div>
                                                                                    @endif
                                                                                </td>
                                                                                @if($preview['show_column_unit'])
                                                                                    <td class="py-2 px-2 text-center text-gray-500">Gói</td>
                                                                                @endif
                                                                                <td class="py-2 px-2 text-center">1</td>
                                                                                <td class="py-2 px-2 text-right">3.500.000 đ</td>
                                                                                <td class="py-2 px-2 text-right font-bold text-gray-900">3.500.000 đ</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="py-2 px-2 text-center text-gray-500">2</td>
                                                                                <td class="py-2 px-2">
                                                                                    <strong class="text-gray-900">Makeup & Làm Tóc Mẹ Cô Dâu</strong>
                                                                                    @if($preview['show_column_schedule'])
                                                                                        <div class="text-[10px] text-gray-500">📅 Lịch hẹn: 07:30 Ngày 28/10/2026</div>
                                                                                    @endif
                                                                                </td>
                                                                                @if($preview['show_column_unit'])
                                                                                    <td class="py-2 px-2 text-center text-gray-500">Người</td>
                                                                                @endif
                                                                                <td class="py-2 px-2 text-center">1</td>
                                                                                <td class="py-2 px-2 text-right">1.000.000 đ</td>
                                                                                <td class="py-2 px-2 text-right font-bold text-gray-900">1.000.000 đ</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                            {{-- 5. KHỐI VIETQR --}}
                                                            @elseif($eId === 'vietqr_box' && $preview['show_vietqr'])
                                                                <div class="p-3 rounded-xl border border-dashed border-amber-300 bg-amber-50/50 flex items-center justify-between gap-3 text-xs w-full">
                                                                    <div class="space-y-0.5">
                                                                        <div class="font-bold text-amber-900 uppercase text-[11px]">VietQR Chuyển Khoản Nhanh</div>
                                                                        <p class="text-gray-600">{{ $settings['bank_name'] ?? 'Techcombank' }}: <strong class="text-amber-700 font-mono">{{ $settings['bank_account_number'] ?? '19032344013012' }}</strong></p>
                                                                        <p class="text-gray-600">Chủ TK: <strong>{{ $settings['bank_account_holder'] ?? 'NGUYEN THI THAO' }}</strong></p>
                                                                    </div>
                                                                    @if($qrUrl)
                                                                        <img src="{{ $qrUrl }}" alt="VietQR" class="w-16 h-16 object-contain rounded border border-white shadow-sm flex-shrink-0">
                                                                    @endif
                                                                </div>

                                                            {{-- 6. KHỐI TOTALS --}}
                                                            @elseif($eId === 'subtotal_row')
                                                                <div class="flex justify-between font-bold text-gray-900 border-b border-gray-200 pb-1 text-xs sm:text-sm w-full">
                                                                    <span>Tổng cộng tiền hàng:</span>
                                                                    <span class="text-amber-700 font-extrabold">{{ number_format($demoGrandTotal) }} đ</span>
                                                                </div>
                                                            @elseif($eId === 'tax_row' && $preview['show_tax_summary'])
                                                                <div class="flex justify-between text-gray-600 text-xs w-full">
                                                                    <span>Thuế GTGT ({{ $demoTaxRate }}%):</span>
                                                                    <span>{{ number_format($demoTaxAmount) }} đ</span>
                                                                </div>
                                                            @elseif($eId === 'deposit_row' && $preview['show_deposit'])
                                                                <div class="flex justify-between text-gray-600 text-xs w-full">
                                                                    <span>Đã đặt cọc trước:</span>
                                                                    <span class="text-emerald-600 font-semibold">- {{ number_format($demoDeposit) }} đ</span>
                                                                </div>
                                                            @elseif($eId === 'remaining_row' && $preview['show_deposit'])
                                                                <div class="flex justify-between text-xs sm:text-sm font-bold text-red-600 pt-1 border-t border-gray-200 w-full">
                                                                    <span>Còn lại cần thanh toán:</span>
                                                                    <span>{{ number_format($demoRemaining) }} đ</span>
                                                                </div>
                                                            @elseif($eId === 'words_row' && $preview['show_amount_in_words'])
                                                                <div class="text-xs text-gray-600 italic py-1 border-t border-dashed border-gray-200 w-full">
                                                                    <strong>Bằng chữ:</strong> {{ \App\Helpers\InvoiceHelper::docTienBangChu($preview['show_deposit'] ? $demoRemaining : $demoGrandTotal) }}
                                                                </div>

                                                            {{-- 7. KHỐI NOTES --}}
                                                            @elseif($eId === 'notes_text' && $preview['show_notes'] && !empty($preview['notes_content']))
                                                                <div class="p-2.5 bg-gray-50 rounded-lg text-xs text-gray-600 border border-gray-100 whitespace-pre-line leading-relaxed w-full">
                                                                    <strong>Lưu ý:</strong> {{ $preview['notes_content'] }}
                                                                </div>
                                                            @elseif($eId === 'thank_you_text' && !empty($preview['footer_thank_you']))
                                                                <div class="text-center my-1 text-xs italic text-gray-500 w-full">
                                                                    {{ $preview['footer_thank_you'] }}
                                                                </div>

                                                            {{-- 8. KHỐI CHỮ KÝ KHÁCH HÀNG (ĐỘC LẬP) --}}
                                                            @elseif($eId === 'signature_customer_part')
                                                                <div class="w-full text-center py-2">
                                                                    <div class="font-bold uppercase text-gray-800 text-xs">KHÁCH HÀNG</div>
                                                                    <div class="text-[10px] text-gray-400 italic">(Ký & ghi rõ họ tên)</div>
                                                                    <div class="h-12"></div>
                                                                    <div class="font-semibold text-gray-700 text-xs">Nguyễn Hoàng Mai</div>
                                                                </div>

                                                            {{-- 9. KHỐI CHỮ KÝ NGƯỜI LẬP / STUDIO (ĐỘC LẬP) --}}
                                                            @elseif($eId === 'signature_creator_part')
                                                                <div class="w-full text-center py-2">
                                                                    <div class="font-bold uppercase text-gray-800 text-xs">NGƯỜI LẬP PHIẾU</div>
                                                                    <div class="text-[10px] text-gray-400 italic">(Ký, họ tên / Đóng dấu)</div>
                                                                    <div class="h-12"></div>
                                                                    <div class="font-semibold text-gray-700 text-xs">{{ $settings['site_name'] ?? 'Thảo Makeup' }}</div>
                                                                </div>

                                                            {{-- 10. CỤM CHỮ KÝ NHIỀU BÊN / DẤU ĐIỆN TỬ --}}
                                                            @elseif($eId === 'signatures_multi_part')
                                                                @if(($preview['signature_type'] ?? '') === 'five_parties')
                                                                    <div class="grid grid-cols-5 gap-2 text-center text-[10px] w-full pt-2">
                                                                        <div>
                                                                            <strong class="text-gray-800 uppercase block">Người lập</strong>
                                                                            <span class="text-gray-400 italic">(Ký, họ tên)</span>
                                                                            <div class="h-10"></div>
                                                                        </div>
                                                                        <div>
                                                                            <strong class="text-gray-800 uppercase block">Người nhận</strong>
                                                                            <span class="text-gray-400 italic">(Ký, họ tên)</span>
                                                                            <div class="h-10"></div>
                                                                        </div>
                                                                        <div>
                                                                            <strong class="text-gray-800 uppercase block">Thủ kho</strong>
                                                                            <span class="text-gray-400 italic">(Ký, họ tên)</span>
                                                                            <div class="h-10"></div>
                                                                        </div>
                                                                        <div>
                                                                            <strong class="text-gray-800 uppercase block">Kế toán</strong>
                                                                            <span class="text-gray-400 italic">(Ký, họ tên)</span>
                                                                            <div class="h-10"></div>
                                                                        </div>
                                                                        <div>
                                                                            <strong class="text-gray-800 uppercase block">Giám đốc</strong>
                                                                            <span class="text-gray-400 italic">(Ký, đóng dấu)</span>
                                                                            <div class="h-10"></div>
                                                                        </div>
                                                                    </div>
                                                                @elseif(($preview['signature_type'] ?? '') === 'digital_stamp')
                                                                    <div class="flex justify-end w-full pt-2">
                                                                        <div class="p-3 border-2 border-emerald-500 bg-emerald-50 text-emerald-800 rounded-lg text-[10px] text-left">
                                                                            <div class="font-bold flex items-center gap-1 text-xs">
                                                                                <x-filament::icon icon="heroicon-s-shield-check" class="w-4 h-4 text-emerald-600" /> Signature Valid
                                                                            </div>
                                                                            <p>Ký bởi: <strong>{{ $settings['site_name'] ?? 'THAO MAKEUP STUDIO' }}</strong></p>
                                                                            <p>Ngày ký: {{ date('d/m/Y H:i:s') }}</p>
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                            {{-- 11. KHỐI LOOKUP --}}
                                                            @elseif($eId === 'lookup_content' && $preview['show_lookup_link'])
                                                                <div class="pt-2 border-t border-gray-200 text-center text-[10px] text-gray-500 w-full">
                                                                    <p>Tra cứu hóa đơn tại: <a href="{{ $preview['lookup_url'] }}" target="_blank" class="text-blue-600 underline">{{ $preview['lookup_url'] }}</a> - Mã tra cứu: <strong>8BFLCX5VBP8J</strong></p>
                                                                </div>
                                                            @endif

                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- KHỞI TẠO VÀ ĐỒNG BỘ GRIDSTACK 2D ENGINE --}}
    <script>
        let invoiceGridstackInstance = null;

        function initOrUpdateInvoiceGridstack() {
            const el = document.getElementById('invoice-grid-canvas');
            if (!el || typeof GridStack === 'undefined') return;

            // Hủy instance cũ nếu đã tồn tại trước khi khởi tạo lại
            if (invoiceGridstackInstance) {
                try {
                    invoiceGridstackInstance.destroy(false);
                } catch (e) {
                    console.warn('Gridstack destroy warning:', e);
                }
                invoiceGridstackInstance = null;
            }

            // Khởi tạo GridStack 12 cột 2 chiều
            invoiceGridstackInstance = GridStack.init({
                column: 12,
                cellHeight: 35,
                margin: 6,
                animate: true,
                float: false,
                disableOneColumnMode: true,
                draggable: {
                    handle: '.block-drag-handle'
                },
                resizable: {
                    handles: 'e, se, s, sw, w'
                }
            }, el);

            // Bắt sự kiện người dùng kéo thả hoặc co giãn kích thước
            invoiceGridstackInstance.on('change', function (event, items) {
                if (!items || items.length === 0) return;
                
                const allItems = invoiceGridstackInstance.getGridItems();
                const newLayout = allItems.map(itemEl => {
                    const node = itemEl.gridstackNode;
                    return {
                        id: itemEl.getAttribute('gs-id') || (node ? node.id : ''),
                        x: node ? node.x : parseInt(itemEl.getAttribute('gs-x') || '0'),
                        y: node ? node.y : parseInt(itemEl.getAttribute('gs-y') || '0'),
                        w: node ? node.w : parseInt(itemEl.getAttribute('gs-w') || '12'),
                        h: node ? node.h : parseInt(itemEl.getAttribute('gs-h') || '3'),
                    };
                }).filter(item => Boolean(item.id));

                if (newLayout.length > 0) {
                    @this.updateGridLayout(newLayout);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initOrUpdateInvoiceGridstack);
        document.addEventListener('livewire:navigated', initOrUpdateInvoiceGridstack);
        document.addEventListener('livewire:initialized', function () {
            initOrUpdateInvoiceGridstack();
            if (window.Livewire) {
                Livewire.hook('morph.updated', () => {
                    setTimeout(initOrUpdateInvoiceGridstack, 60);
                });
                Livewire.hook('commit', () => {
                    setTimeout(initOrUpdateInvoiceGridstack, 60);
                });
                Livewire.on('grid-layout-updated', () => {
                    setTimeout(initOrUpdateInvoiceGridstack, 60);
                });
            }
        });

        // XUẤT BẢN IN THỬ TRỰC TIẾP TỪ CANVAS
        function printInvoicePreview() {
            const container = document.getElementById('visual-invoice-sheet');
            if (!container) return;

            const clone = container.cloneNode(true);
            
            // Xóa sạch các nút điều khiển, thanh công cụ, icon kéo thả và tay cầm resize
            clone.querySelectorAll('.grid-block-header, .element-action-toolbar, .ui-resizable-handle').forEach(el => el.remove());
            
            // Chuyển đổi các khối Gridstack sang CSS Grid chuẩn 12 cột cho trang in
            clone.querySelectorAll('.grid-stack-item').forEach(el => {
                const w = parseInt(el.getAttribute('gs-w') || '12');
                el.className = `invoice-grid-col col-span-${w}`;
                el.style.cssText = `grid-column: span ${w}; border: none; background: transparent; padding: 0; box-shadow: none; margin: 0;`;
            });

            clone.querySelectorAll('.grid-block-card, .grid-block-body, .grid-stack-item-content').forEach(el => {
                el.style.border = 'none';
                el.style.background = 'transparent';
                el.style.padding = '0';
                el.style.boxShadow = 'none';
                el.style.overflow = 'visible';
            });

            const gridWrapper = clone.querySelector('.grid-stack');
            if (gridWrapper) {
                gridWrapper.style.cssText = 'display: grid; grid-template-columns: repeat(12, 1fr); gap: 16px; width: 100%;';
            }

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>In Hóa Đơn</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        body { background: white; padding: 20px; font-family: sans-serif; color: #111; }
                        .grid-stack { display: grid !important; grid-template-columns: repeat(12, 1fr) !important; gap: 16px !important; width: 100% !important; }
                        .col-span-1 { grid-column: span 1 !important; }
                        .col-span-2 { grid-column: span 2 !important; }
                        .col-span-3 { grid-column: span 3 !important; }
                        .col-span-4 { grid-column: span 4 !important; }
                        .col-span-5 { grid-column: span 5 !important; }
                        .col-span-6 { grid-column: span 6 !important; }
                        .col-span-7 { grid-column: span 7 !important; }
                        .col-span-8 { grid-column: span 8 !important; }
                        .col-span-9 { grid-column: span 9 !important; }
                        .col-span-10 { grid-column: span 10 !important; }
                        .col-span-11 { grid-column: span 11 !important; }
                        .col-span-12 { grid-column: span 12 !important; }
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
