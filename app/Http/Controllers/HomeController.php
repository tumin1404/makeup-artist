<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Banner;
use App\Models\Service;
use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với dữ liệu tổng hợp
     */
    public function index()
    {
        // 1. Lấy danh sách Banner đang hoạt động, ưu tiên thứ tự sắp xếp
        $banners = Banner::where('is_active', true)
            ->orderBy('order', 'desc')
            ->take(1)
            ->get();

        // Lấy tối đa 3 dịch vụ được đánh dấu "is_featured"
        $services = \App\Models\Service::where('is_active', true)
            ->where('is_featured', true) 
            ->take(3) // Giới hạn 3 để khớp với giao diện grid 3 cột
            ->get();

        // 3. Lấy 3 bài viết mới nhất cho phần "Tạp chí làm đẹp"
        $latestPosts = Post::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        // Trả về view home cùng với các biến dữ liệu
        return view('home', compact('banners', 'services', 'latestPosts'));
    }

    /**
     * Trang giới thiệu (Static page)
     */
    public function about()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('about', compact('settings'));
    }
}