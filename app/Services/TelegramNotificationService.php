<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramNotificationService
{
    /**
     * Gửi tin nhắn bất kỳ tới Telegram theo Token và Chat ID cấu hình.
     */
    public static function sendMessage(string $message, ?string $botToken = null, ?string $chatId = null): bool
    {
        try {
            $botToken = trim((string) ($botToken ?: Setting::get('telegram_bot_token')));
            $chatId = trim((string) ($chatId ?: Setting::get('telegram_chat_id')));

            if (empty($botToken) || empty($chatId)) {
                return false;
            }

            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
            ]);

            if (!$response->successful()) {
                Log::warning('Telegram message request returned error: ' . $response->body());
                return false;
            }

            return true;
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi tin nhắn Telegram: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi thông báo có đơn đặt lịch mới tới Telegram của chủ website.
     */
    public static function sendBookingNotification(Booking $booking): bool
    {
        try {
            $enabled = Setting::get('telegram_notify_enabled', '0') === '1';
            $botToken = trim((string) Setting::get('telegram_bot_token'));
            $chatId = trim((string) Setting::get('telegram_chat_id'));

            if (!$enabled || empty($botToken) || empty($chatId)) {
                return false;
            }

            NotificationDispatcherService::sendBookingCreated($booking);
            return true;
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi thông báo Telegram đặt lịch: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Gửi tin nhắn thử nghiệm để kiểm tra kết nối Telegram Bot.
     *
     * @return array{success: bool, message: string}
     */
    public static function sendTestNotification(?string $botToken = null, ?string $chatId = null): array
    {
        try {
            $botToken = trim((string) ($botToken ?: Setting::get('telegram_bot_token')));
            $chatId = trim((string) ($chatId ?: Setting::get('telegram_chat_id')));

            if (empty($botToken)) {
                return [
                    'success' => false,
                    'message' => 'Chưa cấu hình Telegram Bot Token. Vui lòng nhập mã Token từ @BotFather.',
                ];
            }

            if (empty($chatId)) {
                return [
                    'success' => false,
                    'message' => 'Chưa cấu hình Telegram Chat ID. Vui lòng nhập Chat ID cá nhân hoặc ID của Group.',
                ];
            }

            $siteName = Setting::get('site_name', 'Studio Website');
            $adminUrl = url('/admin');

            $text = "🔔 <b>[" . htmlspecialchars($siteName) . "] KIỂM TRA KẾT NỐI TELEGRAM BOT</b>\n";
            $text .= "━━━━━━━━━━━━━━━━━━━\n";
            $text .= "✓ Chúc mừng bạn! Bot Telegram đã được kết nối thành công với website.\n";
            $text .= "📱 Khi có khách hàng gửi form đặt lịch mới trên website, tin nhắn báo chuông và rung sẽ tự động gửi tới đây tức thì (dưới 1 giây).\n";
            $text .= "⏰ <b>Thời gian kiểm tra:</b> " . now()->format('H:i:s d/m/Y') . "\n";
            $text .= "━━━━━━━━━━━━━━━━━━━\n";
            $text .= "👉 <a href=\"{$adminUrl}\">Truy cập Trang Quản Trị</a>";

            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Đã gửi tin nhắn thử nghiệm thành công! Vui lòng kiểm tra điện thoại/Telegram của bạn.',
                ];
            }

            $errorData = $response->json();
            $errorDesc = $errorData['description'] ?? $response->body();

            return [
                'success' => false,
                'message' => "Telegram báo lỗi: {$errorDesc}. Vui lòng kiểm tra lại Bot Token hoặc đảm bảo bạn đã bấm /start với Bot!",
            ];
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi test Telegram: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi kết nối tới máy chủ Telegram: ' . $e->getMessage(),
            ];
        }
    }
}
