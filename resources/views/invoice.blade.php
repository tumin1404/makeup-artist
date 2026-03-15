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
            <h1>HOÁ ĐƠN DỊCH VỤ MAKEUP</h1>
            <p>Mã đơn: #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="info">
            <p><strong>Khách hàng:</strong> {{ $booking->customer_name ?? 'Khách lẻ' }}</p>
            <p><strong>Số điện thoại:</strong> {{ $booking->phone ?? '' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tên dịch vụ & Lịch trình</th>
                    <th>Đơn giá</th>
                    <th>SL</th>
                    <th>Thành tiền</th>
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
                            <strong>{{ $item->service_name }}</strong><br>
                            <small>Ngày hẹn: {{ $item->service_date ? \Carbon\Carbon::parse($item->service_date)->format('d/m/Y H:i') : 'Chưa xếp lịch' }}</small>
                            @if($item->description)
                                <br><small>Ghi chú: {{ $item->description }}</small>
                            @endif
                        </td>
                        <td>{{ number_format($item->price) }} đ</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($thanhTien) }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Tổng thanh toán: {{ number_format($total) }} VNĐ
        </div>

        <div class="note">
            <p>Cảm ơn quý khách đã sử dụng dịch vụ!</p>
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