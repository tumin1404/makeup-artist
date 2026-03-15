<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f6f1ec; padding: 40px; }
        .container { max-w-[600px]; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 10px; text-align: center; }
        .btn { display: inline-block; padding: 15px 30px; background-color: #c8a98d; color: #ffffff; text-decoration: none; border-radius: 50px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: #3e2f2f;">Yêu cầu đặt lại mật khẩu</h2>
        <p style="color: #666; line-height: 1.6;">Xin chào <strong>{{ $user->name }}</strong>,<br>Hệ thống nhận được yêu cầu đặt lại mật khẩu cho tài khoản quản trị của bạn. Vui lòng nhấn vào nút bên dưới để thiết lập mật khẩu mới.</p>
        
        <a href="{{ $url }}" class="btn">Đặt Lại Mật Khẩu</a>
        
        <p style="color: #999; font-size: 12px; margin-top: 30px;">Link này sẽ hết hạn trong 60 phút. Nếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này.</p>
    </div>
</body>
</html>