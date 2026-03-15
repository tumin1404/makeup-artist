<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f6f1ec; padding: 40px; }
        .container { max-w-[600px]; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 10px; }
        .alert-box { background-color: #fff3f3; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: #3e2f2f; text-align: center;">Cảnh Báo Đăng Nhập</h2>
        <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
        <p>Hệ thống vừa ghi nhận một lượt đăng nhập thành công vào tài khoản quản trị của bạn.</p>
        
        <div class="alert-box">
            <ul style="list-style: none; padding: 0; margin: 0; color: #555; line-height: 1.8;">
                <li><strong>Thời gian:</strong> {{ $time }}</li>
                <li><strong>Địa chỉ IP:</strong> {{ $ip }}</li>
                <li><strong>Vị trí dự đoán:</strong> {{ $location }}</li>
                <li><strong>Trình duyệt/Thiết bị:</strong> {{ $userAgent }}</li>
            </ul>
        </div>

        <p style="color: #666; font-size: 14px;">Nếu đây là bạn, vui lòng bỏ qua email này. Nếu không, hãy vào Admin đổi mật khẩu ngay lập tức để bảo vệ tài khoản.</p>
    </div>
</body>
</html>