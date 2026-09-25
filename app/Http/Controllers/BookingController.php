<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Service;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        return view('booking', compact('services'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $booking = Booking::create([
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'social_link' => $validated['social_link'] ?? null,
            'service_ids' => $validated['service_ids'],
            'booking_date' => $validated['booking_date'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        $successMsg = \App\Models\Setting::get('booking_success_message', 'Cảm ơn quý khách! Chúng tôi đã nhận được thông tin và sẽ liên hệ lại sớm nhất.');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'data' => [
                    'id' => $booking->id,
                    'customer_name' => $booking->customer_name,
                    'phone' => $booking->phone,
                    'booking_date' => !empty($booking->booking_date) ? date('d/m/Y', strtotime($booking->booking_date)) : '',
                ],
            ]);
        }

        return back()->with('success', $successMsg);
    }
}