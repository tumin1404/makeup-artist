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
use Illuminate\Support\Facades\Gate;

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
        // Tùy chỉnh icon thu gọn / mở rộng sidebar thành icon 3 gạch ngang
        \Filament\Support\Facades\FilamentIcon::register([
            'panels::sidebar.collapse-button' => 'heroicon-o-bars-3',
            'panels::sidebar.expand-button' => 'heroicon-o-bars-3',
        ]);

        // Grant all permissions to super_admin
        Gate::before(function ($user, $ability) {
            return method_exists($user, 'hasRole') && $user->hasRole('super_admin') ? true : null;
        });

        // Nếu app đang chạy qua Cloudflare (có APP_URL dạng https) thì ép dùng HTTPS
        if (str_contains(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        // Nạp cấu hình máy chủ Mail động từ cài đặt hệ thống
        \App\Services\MailSettingService::applyConfig();

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

        // Kéo dữ liệu setting và chia sẻ cho tất cả các file view một cách an toàn và tối ưu cache
        View::composer('*', function ($view) {
            try {
                $settings = \Illuminate\Support\Facades\Cache::rememberForever('site_settings', function () {
                    if (Schema::hasTable('settings')) {
                        return Setting::pluck('value', 'key')->toArray();
                    }
                    return [];
                });
                $view->with('settings', $settings);
            } catch (\Throwable $e) {
                $view->with('settings', []);
            }
        });

        // Đăng ký Observers cho Đặt lịch và Thanh toán
        \App\Models\Booking::observe(\App\Observers\BookingObserver::class);
        \App\Models\Payment::observe(\App\Observers\PaymentObserver::class);

        // Lắng nghe lỗi tác vụ nền (Queue) để gửi thông báo tiếng Việt tới Admin
        \Illuminate\Support\Facades\Queue::failing(function (\Illuminate\Queue\Events\JobFailed $event) {
            \App\Services\SystemNotificationService::notifyJobFailure($event);
        });
    }
}
