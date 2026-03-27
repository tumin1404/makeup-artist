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
        // 1. Validate dữ liệu: Đổi service_id thành mảng service_ids
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'zalo' => 'nullable|string|max:20',
            'social_link' => 'nullable|string|max:255',
            'booking_date' => 'required|date',
            'service_ids'   => 'required|array|min:1', // Phải là mảng và chọn ít nhất 1
            'service_ids.*' => 'exists:services,id',   // Từng ID trong mảng phải hợp lệ
            'message' => 'nullable|string',
        ], [
            'service_ids.required' => 'Vui lòng chọn ít nhất một dịch vụ.',
        ]);

        // 2. Lưu vào DB (Sử dụng service_ids thay vì service_id)
        Booking::create([
            'customer_name' => $request->customer_name,
            'phone' => $request->phone,
            'zalo' => $request->zalo,
            'social_link' => $request->social_link,
            'service_ids' => $request->service_ids, // Lưu mảng vào database
            'booking_date' => $request->booking_date,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thảo đã nhận được thông tin. Thảo sẽ liên hệ lại với bạn sớm nhất nhé!');
    }
}