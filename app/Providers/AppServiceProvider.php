<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

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
        // Báo cho hệ thống dùng form email vừa tạo
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Xác thực tài khoản Admin - Luyện Thị Thảo Makeup')
                ->view('emails.verify-email', ['url' => $url, 'user' => $notifiable]);
        });
        // Thêm đoạn này cho Reset Password
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            $url = filament()->getResetPasswordUrl($token, $notifiable);

            return (new MailMessage)
                ->subject('Đặt lại mật khẩu - Luyện Thị Thảo Makeup')
                ->view('emails.reset-password', ['url' => $url, 'user' => $notifiable]);
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
