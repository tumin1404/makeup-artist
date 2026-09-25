<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MailSettingService
{
    /**
     * Nạp cấu hình Mail từ Database (Setting) vào runtime config của Laravel.
     */
    public static function applyConfig(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $mailDriver = Setting::get('mail_mailer', 'smtp');
            $mailHost = Setting::get('mail_host');

            if (!empty($mailHost)) {
                $port = (int) Setting::get('mail_port', 587);
                $encryption = Setting::get('mail_encryption', 'tls');
                if ($encryption === 'none' || empty($encryption)) {
                    $encryption = null;
                }

                $username = Setting::get('mail_username');
                $password = Setting::get('mail_password');
                $fromAddress = Setting::get('mail_from_address', config('mail.from.address'));
                $fromName = Setting::get('mail_from_name', Setting::get('site_name', config('mail.from.name')));

                Config::set('mail.default', $mailDriver);
                Config::set('mail.mailers.smtp.transport', 'smtp');
                Config::set('mail.mailers.smtp.host', $mailHost);
                Config::set('mail.mailers.smtp.port', $port);
                Config::set('mail.mailers.smtp.encryption', $encryption);
                Config::set('mail.mailers.smtp.username', $username);
                Config::set('mail.mailers.smtp.password', $password);
                Config::set('mail.from.address', $fromAddress ?: 'no-reply@example.com');
                Config::set('mail.from.name', $fromName ?: 'Studio Website');

                // Làm mới instance mail manager để áp dụng cấu hình mới (ngoại trừ khi chạy test để không xóa Mail::fake)
                if (!app()->runningUnitTests() && app()->resolved('mail.manager')) {
                    app()->forgetInstance('mail.manager');
                }
            }
        } catch (Throwable $e) {
            Log::warning('Không thể nạp cấu hình Mail động: ' . $e->getMessage());
        }
    }

    /**
     * Gửi email thử nghiệm để kiểm tra kết nối SMTP.
     *
     * @return array{success: bool, message: string}
     */
    public static function sendTestMail(string $toEmail): array
    {
        try {
            static::applyConfig();

            $siteName = Setting::get('site_name', 'Studio System');
            $host = Config::get('mail.mailers.smtp.host', 'Chưa cấu hình');
            $port = Config::get('mail.mailers.smtp.port', '587');
            $from = Config::get('mail.from.address', 'no-reply@example.com');

            $html = "
                <div style=\"font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;\">
                    <div style=\"background: #c8a98d; color: #ffffff; padding: 24px; text-align: center;\">
                        <h2 style=\"margin: 0; font-size: 20px;\">🧪 Email Thử Nghiệm Kết Nối SMTP</h2>
                        <p style=\"margin: 6px 0 0 0; opacity: 0.9; font-size: 13px;\">{$siteName}</p>
                    </div>
                    <div style=\"padding: 24px; color: #374151; font-size: 14px; line-height: 1.6;\">
                        <p>Xin chào quản trị viên,</p>
                        <p>Đây là email kiểm tra được gửi tự động từ trang quản trị website để xác nhận cấu hình máy chủ Email (SMTP / Domain Mail) của bạn đang hoạt động <strong>hoàn toàn chính xác</strong>.</p>
                        
                        <div style=\"background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 16px; margin: 20px 0;\">
                            <h4 style=\"margin: 0 0 10px 0; color: #111827; font-size: 13px; text-transform: uppercase;\">Thông số kết nối hiện tại:</h4>
                            <p style=\"margin: 4px 0;\">📍 <strong>Máy chủ (Host):</strong> {$host}</p>
                            <p style=\"margin: 4px 0;\">🔌 <strong>Cổng kết nối (Port):</strong> {$port}</p>
                            <p style=\"margin: 4px 0;\">📧 <strong>Email gửi đi (From):</strong> {$from}</p>
                            <p style=\"margin: 4px 0;\">⏰ <strong>Thời gian gửi:</strong> " . now()->format('H:i:s d/m/Y') . "</p>
                        </div>

                        <p style=\"color: #059669; font-weight: bold;\">✓ Hệ thống sẵn sàng cho việc Đặt lại mật khẩu, Cảnh báo đăng nhập và Gửi thông tin đặt lịch!</p>
                    </div>
                    <div style=\"background: #f3f4f6; padding: 12px; text-align: center; color: #9ca3af; font-size: 11px;\">
                        © " . date('Y') . " {$siteName}. All rights reserved.
                    </div>
                </div>
            ";

            Mail::send([], [], function ($message) use ($toEmail, $siteName, $html) {
                $message->to($toEmail)
                    ->subject("🧪 [Test SMTP] Kết nối Email thành công - {$siteName}")
                    ->html($html);
            });

            return [
                'success' => true,
                'message' => "Đã gửi email thử nghiệm thành công tới [{$toEmail}]. Vui lòng kiểm tra hộp thư (cả mục Hộp thư đến và Spam/Quảng cáo).",
            ];
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi email test: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gửi email thất bại: ' . $e->getMessage(),
            ];
        }
    }
}
