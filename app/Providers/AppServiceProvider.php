<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nếu app đang chạy qua Cloudflare (có APP_URL dạng https) thì ép dùng HTTPS
        if (str_contains(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        // Báo cho hệ thống dùng form email vừa tạo
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $siteName = Setting::get('site_name', 'Hệ thống'); // Lấy tên động
            
            return (new MailMessage)
                ->subject("Xác thực tài khoản Admin - {$siteName}")
                ->view('emails.verify-email', ['url' => $url, 'user' => $notifiable, 'siteName' => $siteName]);
        });

        // Thêm đoạn này cho Reset Password
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $siteName = Setting::get('site_name', 'Hệ thống'); // Lấy tên động
            $url = filament()->getResetPasswordUrl($token, $notifiable);

            return (new MailMessage)
                ->subject("Đặt lại mật khẩu - {$siteName}")
                ->view('emails.reset-password', ['url' => $url, 'user' => $notifiable, 'siteName' => $siteName]);
        });

        \BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch::configureUsing(function (\BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch $switch) {
            $switch->locales(['vi', 'en']); // Cài đặt 2 ngôn ngữ Việt và Anh
        });

        // Kéo toàn bộ dữ liệu setting và chia sẻ cho tất cả các file view
        if (Schema::hasTable('settings')) {
            $settings = Setting::pluck('value', 'key')->toArray();
            View::share('settings', $settings);
        }
    }
}
