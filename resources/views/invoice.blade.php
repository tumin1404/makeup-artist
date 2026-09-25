<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config['invoice_title'] ?? 'HÓA ĐƠN' }} - #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    @php
        $paperSize = $config['paper_size'] ?? 'a4';
        $theme = $config['color_theme'] ?? 'luxury';
        $sigType = $config['signature_type'] ?? 'two_parties';
        
        $brandGold = $settings['theme_color_gold'] ?? '#c8a98d';
        $brandDark = $settings['theme_color_dark'] ?? '#3e2f2f';

        // Tính toán số tiền
        $totalItems = 0;
        if (!empty($booking->items) && count($booking->items) > 0) {
            foreach ($booking->items as $it) {
                $totalItems += ($it->price * ($it->quantity ?: 1));
            }
        } else {
            $totalItems = $booking->total_amount ?? 0;
        }

        $taxRate = (!empty($config['show_column_tax']) && isset($config['tax_rate_default'])) ? floatval($config['tax_rate_default']) : 0;
        $taxAmount = $totalItems * ($taxRate / 100);
        $grandTotal = $totalItems + $taxAmount;

        $deposit = floatval($booking->deposit_amount ?? 0);
        if (empty($deposit) && !empty($config['show_deposit']) && $grandTotal > 2000000) {
            // Giả lập tiền cọc 30% nếu có cọc và chưa có trường deposit_amount trong bảng
            $deposit = 0;
        }
        $remaining = max(0, $grandTotal - $deposit);

        $amountToPay = ($deposit > 0 && !empty($config['show_deposit'])) ? $remaining : $grandTotal;

        // Sinh mã VietQR
        $qrCodeUrl = \App\Helpers\InvoiceHelper::generateVietQRUrl(
            $settings['bank_name'] ?? '',
            $settings['bank_account_number'] ?? '',
            $settings['bank_account_holder'] ?? '',
            $amountToPay,
            'HD ' . str_pad($booking->id, 5, '0', STR_PAD_LEFT) . ' ' . preg_replace('/[^0-9]/', '', $booking->phone ?? '')
        );

        $orderedBlocks = $config['block_order'] ?? [
            'seller_header',
            'invoice_meta',
            'buyer_info',
            'items_table',
            'summary_totals',
            'vietqr_banking',
            'notes_terms',
            'signatures',
        ];
    @endphp

    <style>
        :root {
            --brand-gold: {{ $brandGold }};
            --brand-dark: {{ $brandDark }};
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f3f5;
            color: #2b2b2b;
            padding: 30px 15px;
            font-size: 13px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Thanh điều hướng & nút bấm */
        .top-action-bar {
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-print { background-color: #10b981; color: white; }
        .btn-print:hover { background-color: #059669; }
        .btn-image { background-color: #3b82f6; color: white; }
        .btn-image:hover { background-color: #2563eb; }
        .btn-back { background-color: #f3f4f6; color: #4b5563; }
        .btn-back:hover { background-color: #e5e7eb; color: #111827; }

        /* Khung hóa đơn theo khổ giấy */
        .invoice-card {
            background: white;
            margin: 0 auto;
            padding: 45px 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .paper-a4 { max-width: 820px; min-height: 1050px; }
        .paper-a5 { max-width: 680px; min-height: 600px; padding: 30px 35px; font-size: 12px; }
        .paper-k80 { max-width: 380px; padding: 20px 15px; font-size: 11px; border-radius: 0; box-shadow: none; border: 1px solid #ddd; }

        /* THEME STYLES */
        .theme-luxury {
            border-top: 6px solid var(--brand-gold);
        }
        .theme-luxury .title-color { color: var(--brand-dark); font-family: 'Playfair Display', serif; }
        .theme-luxury .accent-text { color: var(--brand-gold); }
        .theme-luxury .highlight-badge { background-color: #fdfbf7; border: 1px dashed var(--brand-gold); }

        .theme-corporate {
            border-top: 6px solid #1d4ed8;
        }
        .theme-corporate .title-color { color: #1e3a8a; }
        .theme-corporate .accent-text { color: #2563eb; }
        .theme-corporate .highlight-badge { background-color: #eff6ff; border: 1px dashed #93c5fd; }

        .theme-classic {
            border: 1.5px solid #374151;
            border-radius: 4px;
        }
        .theme-classic .title-color { color: #111827; }
        .theme-classic .highlight-badge { background-color: #f9fafb; border: 1px solid #e5e7eb; }

        .theme-minimal {
            border: none;
            box-shadow: none;
        }

        /* HEADER & METADATA */
        .seller-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }
        .seller-name {
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            color: #111827;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .seller-meta {
            color: #4b5563;
            font-size: 12px;
            line-height: 1.6;
        }
        .seller-logo img {
            max-height: 65px;
            max-width: 140px;
            object-contain: contain;
        }

        .invoice-title-block {
            text-align: center;
            margin: 20px 0 25px 0;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-subtitle {
            font-size: 12px;
            color: #6b7280;
            font-style: italic;
            margin-top: 2px;
        }
        .invoice-meta-tags {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 8px;
            font-size: 12px;
            color: #4b5563;
        }
        .cqt-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: #9ca3af;
            margin-top: 4px;
        }

        /* KHÁCH HÀNG */
        .buyer-card {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 25px;
        }
        .buyer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 20px;
            font-size: 12.5px;
        }

        /* BẢNG DỊCH VỤ / SẢN PHẨM */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12.5px;
        }
        .items-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
        }
        .items-table td {
            padding: 12px 12px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        /* TỔNG KẾT & THANH TOÁN */
        .summary-box {
            margin-left: auto;
            width: 100%;
            max-width: 420px;
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            color: #4b5563;
            font-size: 13px;
        }
        .summary-row.grand-total {
            border-top: 2px solid #e5e7eb;
            border-bottom: 2px solid #e5e7eb;
            padding: 10px 0;
            margin: 8px 0;
            font-size: 16px;
            font-weight: 800;
            color: #111827;
        }
        .amount-in-words {
            font-style: italic;
            color: #4b5563;
            font-size: 12px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #e5e7eb;
        }

        /* KHUNG VIETQR & BANKING */
        .vietqr-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 12px;
            padding: 16px 20px;
            margin: 25px 0;
            gap: 20px;
        }
        .vietqr-img {
            max-width: 110px;
            max-height: 110px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: white;
            padding: 3px;
        }

        /* GHI CHÚ & LỜI DẶN DÒ */
        .notes-box {
            background-color: #f9fafb;
            border-left: 4px solid var(--brand-gold);
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 11.5px;
            color: #4b5563;
            margin: 20px 0;
            white-space: pre-line;
            line-height: 1.6;
        }

        /* CHỮ KÝ */
        .signatures-grid {
            display: grid;
            margin: 35px 0 20px 0;
            text-align: center;
        }
        .sig-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            min-height: 120px;
        }
        .sig-title {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            color: #1f2937;
        }
        .sig-subtitle {
            font-size: 10.5px;
            color: #9ca3af;
            font-style: italic;
            margin-top: 2px;
        }
        .digital-stamp {
            border: 2px solid #10b981;
            background: #ecfdf5;
            color: #065f46;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 11px;
            text-align: left;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);
            display: inline-block;
        }

        /* FOOTER TRA CỨU */
        .footer-lookup {
            text-align: center;
            font-size: 10.5px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 15px;
            margin-top: 30px;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: white;
                padding: 0;
                color: #000;
            }
            .top-action-bar, .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                min-height: auto !important;
            }
            @page {
                size: {{ $paperSize === 'a5' ? 'A5 landscape' : ($paperSize === 'k80' ? '80mm auto' : 'A4 portrait') }};
                margin: {{ $paperSize === 'k80' ? '2mm' : '10mm' }};
            }
        }
    </style>
</head>
<body>

    {{-- THANH ĐIỀU HƯỚNG NÚT BẤM (KHÔNG IN) --}}
    <div class="top-action-bar no-print" data-html2canvas-ignore>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ url('/admin/bookings') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <span style="font-size: 12px; color: #6b7280; font-weight: 500;">
                Hóa đơn đặt lịch #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }} • {{ $booking->customer_name }}
            </span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="{{ url('/admin/invoice-settings') }}" target="_blank" class="btn btn-back" style="border: 1px solid #d1d5db;">
                <i class="fas fa-sliders-h"></i> Tùy Chỉnh Mẫu
            </a>
            <button type="button" class="btn btn-image" onclick="downloadInvoiceImage()">
                <i class="fas fa-camera"></i> Tải Ảnh PNG
            </button>
            <button type="button" class="btn btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> In Hóa Đơn
            </button>
        </div>
    </div>

    {{-- KHUNG HÓA ĐƠN CHÍNH --}}
    <div class="invoice-card paper-{{ $paperSize }} theme-{{ $theme }}" id="invoice-capture-area">

        @foreach($orderedBlocks as $blockId)

            {{-- 1. SELLER HEADER --}}
            @if($blockId === 'seller_header')
                <div class="seller-header">
                    <div class="seller-info">
                        @if(!empty($config['show_seller_name']))
                            <div class="seller-name">{{ $settings['site_name'] ?? 'MAKEUP ARTIST & BEAUTY STUDIO' }}</div>
                        @endif
                        <div class="seller-meta">
                            @if(!empty($config['show_seller_tax']) && !empty($settings['business_tax_code']))
                                <p><i class="fas fa-barcode" style="width: 14px; opacity: 0.6;"></i> <strong>MST:</strong> {{ $settings['business_tax_code'] }}</p>
                            @endif
                            @if(!empty($config['show_seller_address']))
                                <p><i class="fas fa-map-marker-alt" style="width: 14px; opacity: 0.6;"></i> {{ $settings['address_main'] ?? ($settings['address'] ?? 'Hà Nội, Việt Nam') }}</p>
                            @endif
                            @if(!empty($config['show_seller_phone']) && !empty($settings['hotline']))
                                <p><i class="fas fa-phone" style="width: 14px; opacity: 0.6;"></i> Hotline: <strong>{{ $settings['hotline'] }}</strong> {{ !empty($settings['email']) ? '| Email: ' . $settings['email'] : '' }}</p>
                            @endif
                            @if(!empty($config['show_seller_bank']) && !empty($settings['bank_account_number']))
                                <p><i class="fas fa-university" style="width: 14px; opacity: 0.6;"></i> STK: <strong>{{ $settings['bank_account_number'] }}</strong> - {{ $settings['bank_name'] ?? '' }} ({{ $settings['bank_account_holder'] ?? '' }})</p>
                            @endif
                        </div>
                    </div>
                    @if(!empty($config['show_logo']) && !empty($settings['site_logo']))
                        <div class="seller-logo">
                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="{{ $settings['site_name'] ?? 'Logo' }}">
                        </div>
                    @endif
                </div>

            {{-- 2. INVOICE META & TITLE --}}
            @elseif($blockId === 'invoice_meta')
                <div class="invoice-title-block">
                    <h1 class="invoice-title title-color">{{ $config['invoice_title'] ?: 'HÓA ĐƠN DỊCH VỤ' }}</h1>
                    @if(!empty($config['invoice_subtitle']))
                        <p class="invoice-subtitle">{{ $config['invoice_subtitle'] }}</p>
                    @endif
                    <div class="invoice-meta-tags">
                        <span><i class="far fa-calendar-alt"></i> Ngày: <strong>{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y') }}</strong></span>
                        @if(!empty($config['show_invoice_number']))
                            <span>• Số: <strong>{{ $config['invoice_number_prefix'] ?? '' }}#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</strong></span>
                        @endif
                        @if(!empty($config['show_invoice_symbol']) && !empty($config['invoice_symbol']))
                            <span>• Ký hiệu: <strong>{{ $config['invoice_symbol'] }}</strong></span>
                        @endif
                    </div>
                    @if(!empty($config['show_cqt_code']) && !empty($config['cqt_code']))
                        <div class="cqt-code">Mã CQT: {{ $config['cqt_code'] }}</div>
                    @endif
                </div>

            {{-- 3. BUYER INFO --}}
            @elseif($blockId === 'buyer_info')
                <div class="buyer-card">
                    <div class="buyer-grid">
                        @if(!empty($config['show_buyer_name']))
                            <div><strong>Khách hàng:</strong> {{ $booking->customer_name ?? 'Khách lẻ' }}</div>
                        @endif
                        @if(!empty($config['show_buyer_phone']))
                            <div><strong>Số điện thoại:</strong> {{ $booking->phone ?? 'Chưa cập nhật' }}</div>
                        @endif
                        @if(!empty($config['show_buyer_company']))
                            <div><strong>Đơn vị / Công ty:</strong> {{ $booking->customer_company ?? ($booking->customer_name ?? '') }}</div>
                        @endif
                        @if(!empty($config['show_buyer_tax']))
                            <div><strong>Mã số thuế:</strong> {{ $booking->customer_tax_code ?? '---' }}</div>
                        @endif
                        @if(!empty($config['show_buyer_address']))
                            <div style="grid-column: span 2;"><strong>Địa chỉ:</strong> {{ $booking->address ?? ($settings['address_main'] ?? 'Hà Nội') }}</div>
                        @endif
                        @if(!empty($config['show_payment_method']))
                            <div style="grid-column: span 2;"><strong>Hình thức thanh toán:</strong> {{ $config['payment_method_default'] ?: 'Tiền mặt / Chuyển khoản' }}</div>
                        @endif
                    </div>
                </div>

            {{-- 4. ITEMS TABLE --}}
            @elseif($blockId === 'items_table')
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">STT</th>
                            @if(!empty($config['show_column_code']))
                                <th class="text-left" style="width: 70px;">Mã</th>
                            @endif
                            <th class="text-left">Nội dung dịch vụ / Hàng hóa</th>
                            @if(!empty($config['show_column_unit']))
                                <th class="text-center" style="width: 60px;">ĐVT</th>
                            @endif
                            <th class="text-center" style="width: 50px;">SL</th>
                            <th class="text-right" style="width: 120px;">Đơn giá</th>
                            @if(!empty($config['show_column_discount']))
                                <th class="text-right" style="width: 80px;">CK</th>
                            @endif
                            @if(!empty($config['show_column_tax']))
                                <th class="text-center" style="width: 60px;">Thuế</th>
                            @endif
                            <th class="text-right" style="width: 130px;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($booking->items) && count($booking->items) > 0)
                            @foreach($booking->items as $idx => $item)
                                @php
                                    $itemTotal = $item->price * ($item->quantity ?: 1);
                                @endphp
                                <tr>
                                    <td class="text-center" style="color: #6b7280;">{{ $idx + 1 }}</td>
                                    @if(!empty($config['show_column_code']))
                                        <td class="text-left" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #6b7280;">DV{{ str_pad($item->id ?? ($idx + 1), 2, '0', STR_PAD_LEFT) }}</td>
                                    @endif
                                    <td class="text-left">
                                        <strong style="color: #111827;">{{ $item->service_name }}</strong>
                                        @if(!empty($config['show_column_schedule']) && !empty($item->service_date))
                                            <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                                <i class="far fa-clock"></i> Lịch: {{ \Carbon\Carbon::parse($item->service_date)->format('H:i d/m/Y') }}
                                            </div>
                                        @endif
                                        @if(!empty($item->description))
                                            <div style="font-size: 11px; color: #9ca3af; font-style: italic;">
                                                Ghi chú: {{ $item->description }}
                                            </div>
                                        @endif
                                    </td>
                                    @if(!empty($config['show_column_unit']))
                                        <td class="text-center" style="color: #6b7280;">Gói</td>
                                    @endif
                                    <td class="text-center font-semibold">{{ $item->quantity ?: 1 }}</td>
                                    <td class="text-right">{{ number_format($item->price) }} đ</td>
                                    @if(!empty($config['show_column_discount']))
                                        <td class="text-right" style="color: #9ca3af;">0</td>
                                    @endif
                                    @if(!empty($config['show_column_tax']))
                                        <td class="text-center" style="color: #6b7280;">{{ $taxRate }}%</td>
                                    @endif
                                    <td class="text-right font-bold" style="color: #111827;">{{ number_format($itemTotal) }} đ</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" style="color: #6b7280;">1</td>
                                @if(!empty($config['show_column_code']))
                                    <td class="text-left" style="font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #6b7280;">DV01</td>
                                @endif
                                <td class="text-left">
                                    <strong style="color: #111827;">Dịch vụ Makeup & Làm đẹp</strong>
                                    @if(!empty($booking->booking_date))
                                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                            <i class="far fa-clock"></i> Ngày hẹn: {{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                @if(!empty($config['show_column_unit']))
                                    <td class="text-center" style="color: #6b7280;">Gói</td>
                                </td>
                                @endif
                                <td class="text-center font-semibold">1</td>
                                <td class="text-right">{{ number_format($totalItems) }} đ</td>
                                @if(!empty($config['show_column_discount']))
                                    <td class="text-right" style="color: #9ca3af;">0</td>
                                @endif
                                @if(!empty($config['show_column_tax']))
                                    <td class="text-center" style="color: #6b7280;">{{ $taxRate }}%</td>
                                @endif
                                <td class="text-right font-bold" style="color: #111827;">{{ number_format($totalItems) }} đ</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            {{-- 5. SUMMARY TOTALS --}}
            @elseif($blockId === 'summary_totals')
                <div class="summary-box">
                    @if(!empty($config['show_tax_summary']) && $taxRate > 0)
                        <div class="summary-row">
                            <span>Cộng tiền hàng:</span>
                            <span>{{ number_format($totalItems) }} đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Tiền thuế GTGT ({{ $taxRate }}%):</span>
                            <span>{{ number_format($taxAmount) }} đ</span>
                        </div>
                    @endif

                    <div class="summary-row grand-total">
                        <span>TỔNG TIỀN:</span>
                        <span style="color: var(--brand-dark);">{{ number_format($grandTotal) }} VNĐ</span>
                    </div>

                    @if(!empty($config['show_deposit']) && $deposit > 0)
                        <div class="summary-row" style="color: #059669; font-weight: 600;">
                            <span>Đã đặt cọc:</span>
                            <span>- {{ number_format($deposit) }} đ</span>
                        </div>
                        <div class="summary-row" style="color: #dc2626; font-weight: 700; font-size: 14px;">
                            <span>Còn lại cần thanh toán:</span>
                            <span>{{ number_format($remaining) }} VNĐ</span>
                        </div>
                    @endif

                    @if(!empty($config['show_debt']))
                        <div class="summary-row">
                            <span>Nợ cũ:</span>
                            <span>0 đ</span>
                        </div>
                        <div class="summary-row" style="font-weight: 700;">
                            <span>Tổng nợ sau phiếu:</span>
                            <span>{{ number_format($remaining) }} đ</span>
                        </div>
                    @endif

                    @if(!empty($config['show_amount_in_words']))
                        <div class="amount-in-words">
                            <strong>Số tiền viết bằng chữ:</strong> {{ \App\Helpers\InvoiceHelper::docTienBangChu($amountToPay) }}
                        </div>
                    @endif
                </div>

            {{-- 6. VIETQR BANKING --}}
            @elseif($blockId === 'vietqr_banking' && !empty($config['show_vietqr']) && $qrCodeUrl)
                <div class="vietqr-box highlight-badge">
                    <div style="flex: 1;">
                        <div style="font-weight: 700; text-transform: uppercase; font-size: 12px; margin-bottom: 6px; color: var(--brand-dark);">
                            <i class="fas fa-qrcode" style="color: var(--brand-gold); margin-right: 4px;"></i> Thanh toán chuyển khoản tức thì (VietQR)
                        </div>
                        <p style="font-size: 12px; color: #4b5563; margin-bottom: 2px;">Ngân hàng: <strong>{{ $settings['bank_name'] ?? '' }}</strong></p>
                        <p style="font-size: 12px; color: #4b5563; margin-bottom: 2px;">Số tài khoản: <strong style="color: #d97706; font-family: 'JetBrains Mono', monospace; font-size: 14px;">{{ $settings['bank_account_number'] ?? '' }}</strong></p>
                        <p style="font-size: 12px; color: #4b5563; margin-bottom: 2px;">Chủ tài khoản: <strong>{{ $settings['bank_account_holder'] ?? '' }}</strong></p>
                        <p style="font-size: 11px; color: #6b7280;">Cú pháp: <strong style="color: #111827;">HD {{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }} {{ $booking->phone }}</strong></p>
                    </div>
                    <div>
                        <img src="{{ $qrCodeUrl }}" alt="VietQR" class="vietqr-img">
                    </div>
                </div>

            {{-- 7. NOTES & TERMS --}}
            @elseif($blockId === 'notes_terms')
                @if(!empty($config['show_notes']) && !empty($config['notes_content']))
                    <div class="notes-box">
                        <strong>Ghi chú & Dặn dò:</strong><br>
                        {{ $config['notes_content'] }}
                    </div>
                @endif

                @if(!empty($config['footer_thank_you']))
                    <div style="text-align: center; margin: 20px 0 10px; font-style: italic; color: #6b7280; font-size: 12px;">
                        {{ $config['footer_thank_you'] }}
                    </div>
                @endif

            {{-- 8. SIGNATURES --}}
            @elseif($blockId === 'signatures')
                @if($sigType === 'two_parties')
                    <div class="signatures-grid" style="grid-template-columns: 1fr 1fr;">
                        <div class="sig-col">
                            <div>
                                <div class="sig-title">KHÁCH HÀNG</div>
                                <div class="sig-subtitle">(Ký, ghi rõ họ tên)</div>
                            </div>
                            <div style="margin-top: 60px; font-weight: 600; color: #4b5563;">{{ $booking->customer_name }}</div>
                        </div>
                        <div class="sig-col">
                            <div>
                                <div class="sig-title">NGƯỜI LẬP PHIẾU</div>
                                <div class="sig-subtitle">(Ký, ghi rõ họ tên)</div>
                            </div>
                            <div style="margin-top: 60px; font-weight: 600; color: #4b5563;">{{ $settings['site_name'] ?? 'Thảo Makeup Studio' }}</div>
                        </div>
                    </div>

                @elseif($sigType === 'digital_stamp')
                    <div class="signatures-grid" style="grid-template-columns: 1fr 1fr; align-items: flex-end;">
                        <div class="sig-col">
                            <div>
                                <div class="sig-title">Người mua hàng</div>
                                <div class="sig-subtitle">(Chữ ký số nếu có)</div>
                            </div>
                            <div style="margin-top: 60px;"></div>
                        </div>
                        <div class="sig-col">
                            <div>
                                <div class="sig-title" style="margin-bottom: 8px;">Người bán hàng</div>
                                <div class="digital-stamp">
                                    <div style="font-weight: 700; color: #059669; margin-bottom: 3px;">
                                        <i class="fas fa-check-circle"></i> Signature Valid
                                    </div>
                                    <div>Ký bởi: <strong>{{ $settings['site_name'] ?? 'CÔNG TY TNHH THẢO MAKEUP' }}</strong></div>
                                    <div>Ký ngày: {{ date('d/m/Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($sigType === 'five_parties')
                    <div class="signatures-grid" style="grid-template-columns: repeat(5, 1fr); font-size: 11px;">
                        <div class="sig-col">
                            <div class="sig-title">Người lập</div>
                            <div class="sig-subtitle">(Ký, họ tên)</div>
                            <div style="margin-top: 50px;"></div>
                        </div>
                        <div class="sig-col">
                            <div class="sig-title">Người nhận</div>
                            <div class="sig-subtitle">(Ký, họ tên)</div>
                            <div style="margin-top: 50px;"></div>
                        </div>
                        <div class="sig-col">
                            <div class="sig-title">Thủ kho</div>
                            <div class="sig-subtitle">(Ký, họ tên)</div>
                            <div style="margin-top: 50px;"></div>
                        </div>
                        <div class="sig-col">
                            <div class="sig-title">Kế toán</div>
                            <div class="sig-subtitle">(Ký, họ tên)</div>
                            <div style="margin-top: 50px;"></div>
                        </div>
                        <div class="sig-col">
                            <div class="sig-title">Giám đốc</div>
                            <div class="sig-subtitle">(Đóng dấu)</div>
                            <div style="margin-top: 50px;"></div>
                        </div>
                    </div>
                @endif

            {{-- 9. FOOTER LOOKUP --}}
            @elseif($blockId === 'footer_lookup' && !empty($config['show_lookup_link']))
                <div class="footer-lookup">
                    <p>Tra cứu tại Website: <a href="{{ $config['lookup_url'] }}" target="_blank" style="color: #2563eb; text-decoration: underline;">{{ $config['lookup_url'] }}</a> - Mã tra cứu: <strong>8BFLCX5VBP8J</strong></p>
                    <p style="color: #9ca3af; font-size: 10px; margin-top: 2px;">(Cần kiểm tra, đối chiếu khi lập, giao, nhận hóa đơn)</p>
                </div>
            @endif

        @endforeach

    </div>

    <script>
        function downloadInvoiceImage() {
            const invoice = document.getElementById('invoice-capture-area');
            
            // Tăng scale = 2 để ảnh chụp cực kỳ sắc nét trên điện thoại & máy tính
            html2canvas(invoice, { 
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                let link = document.createElement('a');
                link.download = 'Hoa-Don-{{ $booking->id }}-{{ Str::slug($booking->customer_name) }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>