<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Portfolio;

class PortfolioController extends Controller
{
    public function index()
    {
        // 1. Lấy danh sách danh mục (dùng cho các nút lọc Filter)
        $portfolioCategories = Category::where('type', Category::TYPE_PORTFOLIO)
            ->orderBy('order', 'asc')
            ->get();

        // 2. Lấy toàn bộ ảnh/video đang hoạt động kèm thông tin danh mục
        $portfolios = Portfolio::with('category')
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->latest()
            ->get();
            
        // 3. Truyền cả 2 biến ra View
        return view('portfolio', compact('portfolios', 'portfolioCategories'));
    }
}