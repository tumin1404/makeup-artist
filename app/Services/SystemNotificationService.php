<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class SystemNotificationService
{
    /**
     * Lấy danh sách tất cả các User quản trị viên cần nhận thông báo.
     *
     * @return Collection<int, User>
     */
    public static function getAdminRecipients(): Collection
    {
        try {
            return User::all()->filter(function (User $user) {
                return $user->hasRole('super_admin')
                    || $user->hasRole('admin')
                    || $user->hasRole('panel_user')
                    || (method_exists($user, 'can') && $user->can('access_admin_panel'))
                    || app()->environment('local');
            });
        } catch (Throwable $e) {
            Log::warning('Could not retrieve admin notification recipients: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Gửi một thông báo tới toàn bộ quản trị viên.
     */
    public static function sendToAdmins(Notification $notification): void
    {
        $admins = static::getAdminRecipients();

        if ($admins->isNotEmpty()) {
            try {
                $notification->sendToDatabase($admins);
            } catch (Throwable $e) {
                Log::warning('Failed to send database notification to admins: ' . $e->getMessage());
            }
        }
    }

    /**
     * Thông báo khi có khách hàng đặt lịch mới.
     */
    public static function notifyBookingCreated(Booking $booking): void
    {
        $dateFormatted = $booking->booking_date
            ? \Carbon\Carbon::parse($booking->booking_date)->format('H:i d/m/Y')
            : 'Chưa chốt thời gian';

        $notification = Notification::make()
            ->title("Khách mới đặt lịch: {$booking->customer_name}")
            ->body("SĐT: {$booking->phone} | Ngày hẹn: {$dateFormatted}")
            ->icon('heroicon-o-calendar-days')
            ->iconColor('warning')
            ->actions([
                Action::make('view')
                    ->label('Xem chi tiết')
                    ->url("/admin/bookings/{$booking->id}/edit")
                    ->button(),
            ]);

        static::sendToAdmins($notification);
    }

    /**
     * Thông báo về tình trạng công nợ chưa thanh toán.
     */
    public static function notifyDebtStatus(Booking $booking, float|int $remainingDebt, string $actionType = 'created'): void
    {
        if ($remainingDebt <= 0 || $booking->status === 'canceled') {
            return;
        }

        $formattedDebt = number_format($remainingDebt, 0, ',', '.') . 'đ';
        $formattedTotal = number_format($booking->total_amount ?? 0, 0, ',', '.') . 'đ';

        $title = match ($actionType) {
            'payment' => "Công nợ còn lại sau thanh toán: Đơn #{$booking->id}",
            'update' => "Cập nhật công nợ: Đơn #{$booking->id} - {$booking->customer_name}",
            default => "Đơn có công nợ cần thu: #{$booking->id} - {$booking->customer_name}",
        };

        $notification = Notification::make()
            ->title($title)
            ->body("Khách hàng: {$booking->customer_name} (SĐT: {$booking->phone}) | Tổng đơn: {$formattedTotal} | Còn thiếu: {$formattedDebt}")
            ->icon('heroicon-o-banknotes')
            ->iconColor('danger')
            ->actions([
                Action::make('view_booking')
                    ->label('Xem đơn hàng')
                    ->url("/admin/bookings/{$booking->id}/edit")
                    ->button(),
                Action::make('view_payments')
                    ->label('Thu tiền ngay')
                    ->url("/admin/payments/create?booking_id={$booking->id}")
                    ->color('danger'),
            ]);

        static::sendToAdmins($notification);
    }

    /**
     * Thông báo đăng nhập thành công.
     */
    public static function notifyUserLogin(User $user, string $ip, string $location = 'Không xác định'): void
    {
        $time = now()->format('H:i d/m/Y');

        $notification = Notification::make()
            ->title("Đăng nhập tài khoản: {$user->name}")
            ->body("Tài khoản vừa đăng nhập lúc {$time} từ IP {$ip} ({$location}).")
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->iconColor('success');

        // Gửi cho chính user đó và các super admin
        try {
            $notification->sendToDatabase($user);
        } catch (Throwable $e) {
            Log::warning('Failed to send login notification: ' . $e->getMessage());
        }
    }

    /**
     * Thông báo sự cố / lỗi hệ thống được dịch tự động sang tiếng Việt thân thiện.
     */
    public static function notifySystemError(Throwable|string $exception, ?string $context = null): void
    {
        $translated = SystemErrorTranslator::translate($exception, $context);

        $notification = Notification::make()
            ->title($translated['title'])
            ->body($translated['body'])
            ->icon($translated['icon'])
            ->iconColor($translated['color']);

        static::sendToAdmins($notification);
    }

    /**
     * Thông báo khi một tác vụ nền (Queue Job) bị thất bại.
     */
    public static function notifyJobFailure(JobFailed $event): void
    {
        $jobName = class_basename($event->job->resolveName());
        $friendlyJobName = match ($jobName) {
            'CompressImageJob' => 'Tự động nén tối ưu ảnh',
            'SendLoginAlertJob' => 'Gửi cảnh báo đăng nhập qua email',
            default => $jobName,
        };

        $translated = SystemErrorTranslator::translate($event->exception, "Tác vụ: {$friendlyJobName}");

        $notification = Notification::make()
            ->title("Sự cố tác vụ nền: {$friendlyJobName}")
            ->body($translated['body'])
            ->icon($translated['icon'])
            ->iconColor($translated['color']);

        static::sendToAdmins($notification);
    }
}
