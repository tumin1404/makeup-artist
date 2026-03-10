<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service; // Gọi Model Service

class ServiceController extends Controller
{
    public function index()
    {
        // Lấy danh sách dịch vụ đang hoạt động
        $services = Service::where('is_active', true)->get();
        
        return view('services', compact('services'));
    }
}