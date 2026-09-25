<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\SystemNotificationService;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $booking = $payment->booking;
        if (! $booking || $booking->status === 'canceled') {
            return;
        }

        $totalPaid = $booking->payments()->sum('amount');
        $remaining = ($booking->total_amount ?? 0) - $totalPaid;

        if ($remaining > 0) {
            SystemNotificationService::notifyDebtStatus($booking, $remaining, 'payment');
        }
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        if ($payment->wasChanged('amount')) {
            $booking = $payment->booking;
            if (! $booking || $booking->status === 'canceled') {
                return;
            }

            $totalPaid = $booking->payments()->sum('amount');
            $remaining = ($booking->total_amount ?? 0) - $totalPaid;

            if ($remaining > 0) {
                SystemNotificationService::notifyDebtStatus($booking, $remaining, 'payment');
            }
        }
    }
}
