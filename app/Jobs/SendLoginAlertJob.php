<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendLoginAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 20;

    public function __construct(
        public User $user,
        public ?string $ip = null,
        public ?string $userAgent = null
    ) {
    }

    public function handle(): void
    {
        try {
            $siteName = Setting::get('site_name', 'Hệ thống');
            $ip = $this->ip ?? '127.0.0.1';
            $location = 'Không xác định';

            if ($ip !== '127.0.0.1' && $ip !== '::1') {
                try {
                    $response = Http::timeout(4)->get("http://ip-api.com/json/{$ip}");
                    if ($response->ok() && $response->json('status') === 'success') {
                        $location = $response->json('city') . ', ' . $response->json('country');
                    }
                } catch (Throwable $e) {
                    Log::warning('Login alert IP lookup failed: ' . $e->getMessage());
                }
            } else {
                $location = 'Localhost (Máy tính nội bộ)';
            }

            // Gửi thông báo qua Email, Telegram và Zalo theo cấu hình
            \App\Services\NotificationDispatcherService::sendLoginAlert(
                $this->user,
                $ip,
                $location,
                $this->userAgent ?? 'Unknown'
            );

            // Gửi thông báo trực tiếp vào Admin Panel (Database Notifications - biểu tượng chuông)
            \App\Services\SystemNotificationService::notifyUserLogin($this->user, $ip, $location);

            Log::info('Login alert dispatched successfully for user: ' . $this->user->email);
        } catch (Throwable $e) {
            Log::error('Failed to send login alert: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'exception' => $e,
            ]);
        }
    }
}
