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
        <h2 style="color: #3e2f2f;">Xin chào, {{ $user->name }}!</h2>
        <p style="color: #666; line-height: 1.6;">Tài khoản quản trị của bạn tại <strong>Luyện Thị Thảo Makeup</strong> vừa được tạo. Vui lòng nhấn nút bên dưới để xác thực email và kích hoạt tài khoản.</p>
        
        <a href="{{ $url }}" class="btn">Kích Hoạt Tài Khoản</a>
        
        <p style="color: #999; font-size: 12px; margin-top: 30px;">Nếu nút bấm không hoạt động, hãy copy link sau dán vào trình duyệt: <br> {{ $url }}</p>
    </div>
</body>
</html>