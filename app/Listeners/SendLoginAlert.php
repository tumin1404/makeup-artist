<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class SendLoginAlert
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        
        // Lấy vị trí từ IP (Dùng API miễn phí ip-api)
        $location = 'Không xác định';
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            if ($response->ok() && $response->json('status') === 'success') {
                $location = $response->json('city') . ', ' . $response->json('country');
            }
        } else {
            $location = 'Localhost (Máy tính nội bộ)';
        }

        // Gửi mail trực tiếp dùng giao diện (không cần tạo thêm file Mail class)
        Mail::send('emails.login-alert', [
            'user' => $user,
            'ip' => $ip,
            'location' => $location,
            'userAgent' => $userAgent,
            'time' => now()->format('H:i d/m/Y')
        ], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Cảnh báo bảo mật: Đăng nhập tài khoản - Luyện Thị Thảo Makeup');
        });
    }
}
