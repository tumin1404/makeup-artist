<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationDispatcherService
{
    /**
     * Thay thế biến giữ chỗ trong chuỗi mẫu nội dung.
     */
    public static function replacePlaceholders(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', (string) $value, $template);
            $template = str_replace('{$' . $key . '}', (string) $value, $template);
        }
        return $template;
    }

    /**
     * Gửi toàn bộ thông báo khi có đơn đặt lịch mới (Email, Telegram, Zalo).
     */
    public static function sendBookingCreated(Booking $booking): void
    {
        try {
            $siteName = Setting::get('site_name', 'Studio Makeup');
            $adminUrl = url("/admin/bookings/{$booking->id}/edit");
            $dateFormatted = $booking->booking_date
                ? \Carbon\Carbon::parse($booking->booking_date)->format('H:i d/m/Y')
                : 'Chưa chốt thời gian';
            $totalAmount = $booking->total_amount
                ? number_format($booking->total_amount, 0, ',', '.') . ' VNĐ'
                : 'Chưa định giá / Tư vấn';

            $servicesList = 'Dịch vụ tùy chọn / Tư vấn';
            if (!empty($booking->service_ids) && is_array($booking->service_ids)) {
                $names = \App\Models\Service::whereIn('id', $booking->service_ids)->pluck('name')->join(', ');
                if (!empty($names)) {
                    $servicesList = $names;
                }
            } elseif ($booking->relationLoaded('items') || method_exists($booking, 'items')) {
                try {
                    $itemNames = $booking->items()->pluck('service_name')->filter()->join(', ');
                    if (!empty($itemNames)) {
                        $servicesList = $itemNames;
                    }
                } catch (Throwable $e) {
                    // ignore
                }
            }

            $placeholders = [
                'site_name' => $siteName,
                'booking_id' => $booking->id,
                'customer_name' => $booking->customer_name,
                'phone' => $booking->phone,
                'services' => $servicesList,
                'booking_date' => $dateFormatted,
                'total_amount' => $totalAmount,
                'notes' => $booking->notes ?: 'Không có',
                'created_at' => now()->format('H:i:s d/m/Y'),
                'admin_url' => $adminUrl,
            ];

            // 1. Gửi Telegram
            $telegramEnabled = Setting::get('telegram_notify_enabled', '0') === '1';
            $telegramBookingEnabled = Setting::get('telegram_notify_booking_enabled', '1') === '1';
            if ($telegramEnabled && $telegramBookingEnabled) {
                $rawTemplate = Setting::get('telegram_template_booking', '');
                if (empty(trim($rawTemplate))) {
                    $rawTemplate = "✨ <b>[{site_name}] CÓ ĐƠN ĐẶT LỊCH MỚI!</b>\n"
                        . "━━━━━━━━━━━━━━━━━━━\n"
                        . "👤 <b>Khách hàng:</b> {customer_name}\n"
                        . "📞 <b>Số điện thoại:</b> <a href=\"tel:{phone}\">{phone}</a>\n"
                        . "💄 <b>Dịch vụ:</b> {services}\n"
                        . "📅 <b>Thời gian hẹn:</b> {booking_date}\n"
                        . "💵 <b>Tạm tính / Cọc:</b> {total_amount}\n"
                        . "📝 <b>Ghi chú:</b> {notes}\n"
                        . "⏰ <b>Thời gian đặt:</b> {created_at}\n"
                        . "━━━━━━━━━━━━━━━━━━━\n"
                        . "👉 <a href=\"{admin_url}\"><b>Mở trang quản trị để xử lý đơn</b></a>";
                }

                $telegramMsg = self::replacePlaceholders($rawTemplate, [
                    'site_name' => htmlspecialchars($siteName),
                    'customer_name' => htmlspecialchars($booking->customer_name),
                    'phone' => htmlspecialchars($booking->phone),
                    'services' => htmlspecialchars($servicesList),
                    'booking_date' => $dateFormatted,
                    'total_amount' => $totalAmount,
                    'notes' => htmlspecialchars($booking->notes ?: 'Không có'),
                    'created_at' => now()->format('H:i:s d/m/Y'),
                    'admin_url' => $adminUrl,
                ]);

                TelegramNotificationService::sendMessage($telegramMsg);
            }

            // 2. Gửi Email
            $mailBookingEnabled = Setting::get('mail_notify_booking_enabled', '1') === '1';
            if ($mailBookingEnabled) {
                $subject = Setting::get('mail_notify_booking_subject', "[{$siteName}] Xác nhận đơn đặt lịch mới từ {customer_name}");
                $subject = self::replacePlaceholders($subject, $placeholders);

                $body = Setting::get('mail_notify_booking_body', '');
                if (empty(trim($body))) {
                    $body = "Xin chào {customer_name},\n\nCảm ơn bạn đã tin tưởng và đặt lịch tại {site_name}.\nThông tin chi tiết lịch hẹn của bạn:\n- Dịch vụ: {services}\n- Ngày giờ hẹn: {booking_date}\n- Số điện thoại: {phone}\n- Tạm tính: {total_amount}\n- Ghi chú: {notes}\n\nChúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận và chuẩn bị chu đáo nhất.\nTrân trọng,\n{site_name}";
                }
                $body = self::replacePlaceholders($body, $placeholders);

                // Gửi tới email quản trị viên hoặc email khách (nếu có)
                $adminEmail = Setting::get('mail_from_address') ?: User::first()?->email;
                if ($adminEmail) {
                    self::sendRawEmail($adminEmail, $subject, $body, $siteName);
                }
            }

            // 3. Gửi Zalo (Nếu kích hoạt)
            $zaloEnabled = Setting::get('zalo_notify_enabled', '0') === '1';
            $zaloBookingEnabled = Setting::get('zalo_notify_booking_enabled', '0') === '1';
            if ($zaloEnabled && $zaloBookingEnabled) {
                Log::info('Zalo ZNS booking notification queued for booking #' . $booking->id);
            }
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi thông báo đơn đặt lịch mới: ' . $e->getMessage());
        }
    }

    /**
     * Gửi thông báo khi trạng thái đơn đặt lịch thay đổi (Xác nhận, Đang xử lý, Hủy, Hoàn thành).
     */
    public static function sendBookingStatusChanged(Booking $booking, ?string $oldStatus, string $newStatus): void
    {
        try {
            $siteName = Setting::get('site_name', 'Studio Makeup');
            $adminUrl = url("/admin/bookings/{$booking->id}/edit");
            $dateFormatted = $booking->booking_date
                ? \Carbon\Carbon::parse($booking->booking_date)->format('H:i d/m/Y')
                : 'Chưa chốt thời gian';

            $statusLabels = [
                'pending' => 'Chờ xử lý',
                'confirmed' => 'Đã xác nhận',
                'completed' => 'Đã hoàn thành',
                'canceled' => 'Đã hủy',
            ];
            $newStatusLabel = $statusLabels[$newStatus] ?? $newStatus;
            $oldStatusLabel = $statusLabels[$oldStatus] ?? ($oldStatus ?: 'Mới tạo');

            $servicesList = 'Dịch vụ tùy chọn';
            if (!empty($booking->service_ids) && is_array($booking->service_ids)) {
                $names = \App\Models\Service::whereIn('id', $booking->service_ids)->pluck('name')->join(', ');
                if (!empty($names)) {
                    $servicesList = $names;
                }
            }

            $placeholders = [
                'site_name' => $siteName,
                'booking_id' => $booking->id,
                'customer_name' => $booking->customer_name,
                'phone' => $booking->phone,
                'services' => $servicesList,
                'booking_date' => $dateFormatted,
                'old_status' => $oldStatusLabel,
                'new_status' => $newStatusLabel,
                'admin_url' => $adminUrl,
            ];

            // 1. Gửi Telegram
            $telegramEnabled = Setting::get('telegram_notify_enabled', '0') === '1';
            $telegramStatusEnabled = Setting::get('telegram_notify_status_enabled', '1') === '1';
            if ($telegramEnabled && $telegramStatusEnabled) {
                $rawTemplate = Setting::get('telegram_template_status', '');
                if (empty(trim($rawTemplate))) {
                    $rawTemplate = "🔄 <b>[{site_name}] CẬP NHẬT TRẠNG THÁI LỊCH HẸN</b>\n"
                        . "━━━━━━━━━━━━━━━━━━━\n"
                        . "🆔 <b>Mã đơn:</b> #{booking_id}\n"
                        . "👤 <b>Khách hàng:</b> {customer_name} ({phone})\n"
                        . "📊 <b>Trạng thái mới:</b> <b>{new_status}</b>\n"
                        . "📅 <b>Thời gian hẹn:</b> {booking_date}\n"
                        . "💄 <b>Dịch vụ:</b> {services}\n"
                        . "━━━━━━━━━━━━━━━━━━━\n"
                        . "👉 <a href=\"{admin_url}\"><b>Xem chi tiết lịch hẹn</b></a>";
                }

                $telegramMsg = self::replacePlaceholders($rawTemplate, [
                    'site_name' => htmlspecialchars($siteName),
                    'booking_id' => $booking->id,
                    'customer_name' => htmlspecialchars($booking->customer_name),
                    'phone' => htmlspecialchars($booking->phone),
                    'services' => htmlspecialchars($servicesList),
                    'booking_date' => $dateFormatted,
                    'old_status' => htmlspecialchars($oldStatusLabel),
                    'new_status' => htmlspecialchars($newStatusLabel),
                    'admin_url' => $adminUrl,
                ]);

                TelegramNotificationService::sendMessage($telegramMsg);
            }

            // 2. Gửi Email
            $mailStatusEnabled = Setting::get('mail_notify_status_enabled', '1') === '1';
            if ($mailStatusEnabled) {
                $subject = Setting::get('mail_notify_status_subject', "[{$siteName}] Cập nhật trạng thái đơn đặt lịch #{booking_id}: {new_status}");
                $subject = self::replacePlaceholders($subject, $placeholders);

                $body = Setting::get('mail_notify_status_body', '');
                if (empty(trim($body))) {
                    $body = "Xin chào {customer_name},\n\nLịch hẹn #{booking_id} của bạn tại {site_name} đã được cập nhật trạng thái mới: {new_status}.\n- Dịch vụ: {services}\n- Thời gian hẹn: {booking_date}\n- Số điện thoại: {phone}\n\nNếu bạn cần hỗ trợ thêm, vui lòng liên hệ trực tiếp với chúng tôi.\nTrân trọng,\n{site_name}";
                }
                $body = self::replacePlaceholders($body, $placeholders);

                $adminEmail = Setting::get('mail_from_address') ?: User::first()?->email;
                if ($adminEmail) {
                    self::sendRawEmail($adminEmail, $subject, $body, $siteName);
                }
            }
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi thông báo cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    /**
     * Gửi cảnh báo đăng nhập mới qua Email, Telegram và Zalo.
     */
    public static function sendLoginAlert(User $user, string $ip, string $location, string $userAgent): void
    {
        try {
            $siteName = Setting::get('site_name', 'Studio Makeup');
            $time = now()->format('H:i:s d/m/Y');

            $placeholders = [
                'site_name' => $siteName,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'ip_address' => $ip,
                'location' => $location,
                'device' => $userAgent,
                'time' => $time,
            ];

            // 1. Telegram Alert
            $telegramEnabled = Setting::get('telegram_notify_enabled', '0') === '1';
            $telegramLoginEnabled = Setting::get('telegram_notify_login_enabled', '1') === '1';
            if ($telegramEnabled && $telegramLoginEnabled) {
                $rawTemplate = Setting::get('telegram_template_login', '');
                if (empty(trim($rawTemplate))) {
                    $rawTemplate = "🔐 <b>[{site_name}] CẢNH BÁO ĐĂNG NHẬP MỚI!</b>\n"
                        . "━━━━━━━━━━━━━━━━━━━\n"
                        . "👤 <b>Tài khoản:</b> {user_name} ({user_email})\n"
                        . "🌐 <b>Địa chỉ IP:</b> <code>{ip_address}</code>\n"
                        . "📍 <b>Vị trí:</b> {location}\n"
                        . "💻 <b>Thiết bị:</b> {device}\n"
                        . "⏰ <b>Thời gian:</b> {time}\n"
                        . "━━━━━━━━━━━━━━━━━━━\n"
                        . "⚠️ <i>Nếu không phải bạn, hãy đổi mật khẩu admin ngay lập tức!</i>";
                }

                $telegramMsg = self::replacePlaceholders($rawTemplate, [
                    'site_name' => htmlspecialchars($siteName),
                    'user_name' => htmlspecialchars($user->name),
                    'user_email' => htmlspecialchars($user->email),
                    'ip_address' => htmlspecialchars($ip),
                    'location' => htmlspecialchars($location),
                    'device' => htmlspecialchars($userAgent),
                    'time' => $time,
                ]);

                TelegramNotificationService::sendMessage($telegramMsg);
            }

            // 2. Email Alert
            $mailLoginEnabled = Setting::get('mail_notify_login_enabled', '1') === '1';
            if ($mailLoginEnabled && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $subject = Setting::get('mail_notify_login_subject', "[{$siteName}] Cảnh báo bảo mật: Đăng nhập tài khoản quản trị");
                $subject = self::replacePlaceholders($subject, $placeholders);

                $body = Setting::get('mail_notify_login_body', '');
                if (empty(trim($body))) {
                    $body = "Xin chào {user_name},\n\nHệ thống ghi nhận tài khoản {user_email} vừa đăng nhập vào trang quản trị {site_name}.\n- Thời gian: {time}\n- Địa chỉ IP: {ip_address}\n- Vị trí ước tính: {location}\n- Thiết bị: {device}\n\nNếu đây là bạn, vui lòng bỏ qua thư này. Nếu không phải bạn thực hiện, hãy đổi mật khẩu quản trị ngay lập tức để bảo vệ tài khoản.";
                }
                $body = self::replacePlaceholders($body, $placeholders);

                self::sendRawEmail($user->email, $subject, $body, $siteName);
            }
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi cảnh báo đăng nhập: ' . $e->getMessage());
        }
    }

    /**
     * Gửi email đặt lại mật khẩu (Password Reset - chuyên biệt Email).
     */
    public static function sendPasswordReset(User $user, string $resetUrl, int $expireMinutes = 60): void
    {
        try {
            $mailResetEnabled = Setting::get('mail_notify_password_reset_enabled', '1') === '1';
            if (!$mailResetEnabled) {
                return;
            }

            $siteName = Setting::get('site_name', 'Studio Makeup');
            $placeholders = [
                'site_name' => $siteName,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'reset_url' => $resetUrl,
                'expire_minutes' => $expireMinutes,
            ];

            $subject = Setting::get('mail_notify_password_reset_subject', "[{$siteName}] Hướng dẫn đặt lại mật khẩu tài khoản");
            $subject = self::replacePlaceholders($subject, $placeholders);

            $body = Setting::get('mail_notify_password_reset_body', '');
            if (empty(trim($body))) {
                $body = "Xin chào {user_name},\n\nBạn nhận được email này vì hệ thống ghi nhận yêu cầu đặt lại mật khẩu cho tài khoản {user_email} tại {site_name}.\nVui lòng nhấp vào đường link bên dưới để thiết lập mật khẩu mới (link có hiệu lực trong {expire_minutes} phút):\n{reset_url}\n\nNếu bạn không gửi yêu cầu này, vui lòng bỏ qua email.";
            }
            $body = self::replacePlaceholders($body, $placeholders);

            self::sendRawEmail($user->email, $subject, $body, $siteName, $resetUrl);
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi email đặt lại mật khẩu: ' . $e->getMessage());
        }
    }

    /**
     * Gửi email định dạng HTML chuẩn với giao diện đẹp mắt.
     */
    public static function sendRawEmail(string $toEmail, string $subject, string $bodyContent, string $siteName, ?string $buttonUrl = null): bool
    {
        try {
            MailSettingService::applyConfig();

            Mail::send([], [], function ($message) use ($toEmail, $subject, $bodyContent, $siteName, $buttonUrl) {
                $formattedHtml = nl2br(htmlspecialchars($bodyContent));
                
                // Nếu có URL đặt lại mật khẩu hoặc link, render nút bấm đẹp mắt
                $buttonHtml = '';
                if ($buttonUrl) {
                    $buttonHtml = "<div style='text-align: center; margin: 30px 0;'>
                        <a href='{$buttonUrl}' style='background: #d97706; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 14px; display: inline-block;'>Xác Nhận & Đặt Lại Mật Khẩu</a>
                    </div>";
                }

                $html = "<!DOCTYPE html>
                <html>
                <head>
                    <meta charset='utf-8'>
                    <style>
                        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 30px 15px; margin: 0; }
                        .mail-container { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
                        .mail-header { background: linear-gradient(135deg, #1e293b, #0f172a); color: #ffffff; padding: 24px 30px; text-align: center; }
                        .mail-header h2 { margin: 0; font-size: 18px; color: #fbbf24; }
                        .mail-body { padding: 30px; color: #334155; font-size: 14px; line-height: 1.7; }
                        .mail-footer { background-color: #f8fafc; padding: 18px 30px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
                    </style>
                </head>
                <body>
                    <div class='mail-container'>
                        <div class='mail-header'>
                            <h2>✨ " . htmlspecialchars($siteName) . "</h2>
                        </div>
                        <div class='mail-body'>
                            {$formattedHtml}
                            {$buttonHtml}
                        </div>
                        <div class='mail-footer'>
                            © " . date('Y') . " " . htmlspecialchars($siteName) . ". Thư thông báo tự động từ hệ thống website.
                        </div>
                    </div>
                </body>
                </html>";

                $message->to($toEmail)
                        ->subject($subject)
                        ->html($html);
            });

            return true;
        } catch (Throwable $e) {
            Log::error('Lỗi khi gửi email raw: ' . $e->getMessage());
            return false;
        }
    }
}
