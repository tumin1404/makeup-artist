<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio; // Gọi Model Portfolio ra làm việc

class PortfolioController extends Controller
{
    public function index()
    {
        // Lấy toàn bộ ảnh/video đang được bật (is_active = true)
        // Ưu tiên sắp xếp theo sort_order, sau đó là mới nhất lên đầu
        $portfolios = Portfolio::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->latest()
            ->get();
            
        return view('portfolio', compact('portfolios'));
    }
}