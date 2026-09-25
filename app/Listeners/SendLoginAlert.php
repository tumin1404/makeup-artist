<?php

namespace App\Listeners;

use App\Jobs\SendLoginAlertJob;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;

class SendLoginAlert
{
    /**
     * Xử lý sự kiện đăng nhập tài khoản.
     * Chỉ kích hoạt cảnh báo khi người dùng đăng nhập vào khu vực Quản trị Admin (Filament Admin Panel).
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $request = request();
        $ip = $request?->ip() ?? '127.0.0.1';
        $userAgent = $request?->userAgent() ?? 'Unknown';

        // 1. Chỉ phát cảnh báo khi người dùng thực hiện đăng nhập vào khu vực Quản trị Admin
        // Tuyệt đối không gửi cảnh báo khi người dùng duyệt xem giao diện trang chủ, blog, dịch vụ công khai phía ngoài
        $isAdminArea = false;
        if ($request) {
            $isAdminArea = $request->is('admin*')
                || ($request->route() && str_starts_with($request->route()->getName() ?? '', 'filament.'));
        } elseif (app()->runningInConsole()) {
            $isAdminArea = true;
        }

        if (!$isAdminArea) {
            return;
        }

        // 2. Chỉ gửi cảnh báo đối với tài khoản có quyền truy cập trang Quản trị Admin
        if (method_exists($user, 'canAccessPanel') && !app()->runningInConsole()) {
            $adminPanel = Filament::getPanel('admin');
            if ($adminPanel && !$user->canAccessPanel($adminPanel)) {
                return;
            }
        }

        // 3. Khóa đệm chống spam (Rate Limiting Cooldown):
        // Không gửi lại cảnh báo cho cùng 1 user + 1 IP trong vòng 5 phút
        $cacheKey = "login_alert_cooldown_{$user->id}_" . md5($ip);
        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->addMinutes(5));

        SendLoginAlertJob::dispatch($user, $ip, $userAgent);
    }
}
