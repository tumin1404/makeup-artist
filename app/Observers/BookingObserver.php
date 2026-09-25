<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\NotificationDispatcherService;
use App\Services\SystemNotificationService;
use App\Services\TelegramNotificationService;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // 1. Thông báo nội bộ trong Admin Panel (chuông thông báo)
        SystemNotificationService::notifyBookingCreated($booking);

        // 2. Gửi thông báo qua Email, Telegram Bot, Zalo
        NotificationDispatcherService::sendBookingCreated($booking);

        // 3. Nếu đơn có số tiền lớn hơn 0, kiểm tra và thông báo công nợ
        if ($booking->total_amount > 0) {
            $paid = $booking->payments()->sum('amount');
            $remaining = $booking->total_amount - $paid;
            if ($remaining > 0) {
                SystemNotificationService::notifyDebtStatus($booking, $remaining, 'created');
            }
        }
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Gửi thông báo khi trạng thái đơn thay đổi
        if ($booking->wasChanged('status')) {
            NotificationDispatcherService::sendBookingStatusChanged(
                $booking,
                $booking->getOriginal('status'),
                $booking->status
            );
        }

        // Nếu tổng tiền hợp đồng hoặc trạng thái thay đổi, kiểm tra công nợ
        if ($booking->wasChanged('total_amount') || $booking->wasChanged('status')) {
            if ($booking->status !== 'canceled' && $booking->total_amount > 0) {
                $paid = $booking->payments()->sum('amount');
                $remaining = $booking->total_amount - $paid;
                if ($remaining > 0) {
                    SystemNotificationService::notifyDebtStatus($booking, $remaining, 'update');
                }
            }
        }
    }
}
