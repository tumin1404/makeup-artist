<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoá Đơn Dịch Vụ</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; padding: 20px; color: #333; }
        
        /* Khu vực nút bấm (Sẽ bị ẩn khi in và khi xuất ảnh) */
        .action-buttons { text-align: center; margin-bottom: 20px; }
        .btn { padding: 10px 20px; margin: 0 10px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; color: white; }
        .btn-print { background-color: #10b981; }
        .btn-image { background-color: #3b82f6; }

        /* Khung hóa đơn chính */
        .invoice-box { max-width: 800px; margin: auto; padding: 40px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .info p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .total { text-align: right; margin-top: 20px; font-size: 1.5em; font-weight: bold; color: #d97706; }
        .note { margin-top: 50px; font-style: italic; text-align: center; color: #666; }
        
        @media print { 
            body { background: white; padding: 0; }
            .action-buttons { display: none; } /* Ẩn nút khi in */
            .invoice-box { box-shadow: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    
    <div class="action-buttons" data-html2canvas-ignore> <button class="btn btn-print" onclick="window.print()">🖨️ In Hóa Đơn</button>
        <button class="btn btn-image" onclick="downloadImage()">📸 Tải Ảnh Hóa Đơn</button>
    </div>

    <div class="invoice-box" id="invoice-content">
        <div class="header">
            @if(!empty($settings['site_logo']))
                <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="{{ $settings['site_name'] ?? 'Logo' }}" style="max-height: 60px; margin-bottom: 15px;">
            @endif
            <h1 style="margin: 0; font-size: 24px; text-transform: uppercase;">{{ $settings['site_name'] ?? 'HOÁ ĐƠN DỊCH VỤ' }}</h1>
            <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Mã hoá đơn: #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
            @if(!empty($settings['hotline']) || !empty($settings['address_main']) || !empty($settings['address']))
                <p style="margin: 5px 0 0; color: #888; font-size: 12px;">
                    {{ !empty($settings['hotline']) ? 'Hotline: ' . $settings['hotline'] : '' }}
                    {{ !empty($settings['address_main']) ? ' | Đ/C: ' . $settings['address_main'] : (!empty($settings['address']) ? ' | ' . $settings['address'] : '') }}
                </p>
            @endif
        </div>

        <div class="info">
            <p><strong>Khách hàng:</strong> {{ $booking->customer_name ?? 'Khách lẻ' }}</p>
            <p><strong>Số điện thoại:</strong> {{ $booking->phone ?? 'Chưa cập nhật' }}</p>
            @if(!empty($booking->booking_date))
                <p><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y H:i') }}</p>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tên dịch vụ & Lịch trình</th>
                    <th style="width: 130px; text-align: right;">Đơn giá</th>
                    <th style="width: 50px; text-align: center;">SL</th>
                    <th style="width: 140px; text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($booking->items as $item)
                    @php 
                        $thanhTien = $item->price * $item->quantity;
                        $total += $thanhTien;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $item->service_name }}</strong>
                            @if($item->service_date)
                                <br><small style="color: #666;">Ngày: {{ \Carbon\Carbon::parse($item->service_date)->format('d/m/Y H:i') }}</small>
                            @endif
                            @if($item->description)
                                <br><small style="color: #888; font-style: italic;">Ghi chú: {{ $item->description }}</small>
                            @endif
                        </td>
                        <td style="text-align: right;">{{ number_format($item->price) }} đ</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($thanhTien) }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Tổng thanh toán: {{ number_format($total ?: ($booking->total_amount ?? 0)) }} VNĐ
        </div>

        {{-- THÔNG TIN CHUYỂN KHOẢN NGÂN HÀNG NẾU CÓ --}}
        @if(!empty($settings['bank_name']) || !empty($settings['bank_account_number']))
            <div style="margin-top: 30px; padding: 15px; background: #fdfbf7; border: 1px dashed #c8a98d; border-radius: 8px; font-size: 13px;">
                <h4 style="margin: 0 0 8px; color: #3e2f2f; text-transform: uppercase;">Thông tin thanh toán chuyển khoản</h4>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        @if(!empty($settings['bank_name'])) <p style="margin: 3px 0;"><strong>Ngân hàng:</strong> {{ $settings['bank_name'] }}</p> @endif
                        @if(!empty($settings['bank_account_number'])) <p style="margin: 3px 0;"><strong>Số tài khoản:</strong> <span style="font-size: 15px; color: #d97706; font-weight: bold;">{{ $settings['bank_account_number'] }}</span></p> @endif
                        @if(!empty($settings['bank_account_holder'])) <p style="margin: 3px 0;"><strong>Chủ tài khoản:</strong> {{ $settings['bank_account_holder'] }}</p> @endif
                        <p style="margin: 3px 0; color: #666; font-size: 11px;">Cú pháp CK: <strong>HD {{ $booking->id }} {{ $booking->phone }}</strong></p>
                    </div>
                    @if(!empty($settings['bank_qr_image']))
                        <img src="{{ asset('storage/' . $settings['bank_qr_image']) }}" alt="QR Code" style="max-height: 90px; border-radius: 4px; border: 1px solid #ddd;">
                    @endif
                </div>
            </div>
        @endif

        <div class="note">
            <p>{!! nl2br(e($settings['invoice_footer_note'] ?? 'Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của chúng tôi!')) !!}</p>
        </div>
    </div>

    <script>
        function downloadImage() {
            const invoice = document.getElementById('invoice-content');
            
            // Dùng html2canvas chụp lại div invoice-content (Tăng scale để ảnh nét hơn khi zoom trên điện thoại)
            html2canvas(invoice, { scale: 2 }).then(canvas => {
                let link = document.createElement('a');
                link.download = 'Hoa-Don-Makeup-{{ $booking->id }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>