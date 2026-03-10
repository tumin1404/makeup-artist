<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        return view('booking', compact('services'));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'zalo' => 'nullable|string|max:20',
            'social_link' => 'nullable|string|max:255',
            'booking_date' => 'required|date',
            'service_id' => 'required|exists:services,id',
            'message' => 'nullable|string',
        ]);

        // 2. Lưu trực tiếp vào từng cột
        Booking::create([
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'zalo' => $request->zalo,
            'social_link' => $request->social_link,
            'service_id' => $request->service_id,
            'booking_date' => $request->booking_date,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Đã gửi yêu cầu đặt lịch thành công!');
    }
}