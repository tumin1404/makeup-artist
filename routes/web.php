<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PortfolioController;

/*
|--------------------------------------------------------------------------
| 1. Giao diện Người dùng (Public Routes)
|--------------------------------------------------------------------------
| Các route này ai cũng có thể truy cập để xem thông tin.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gioi-thieu', [HomeController::class, 'about'])->name('about');

// Dịch vụ & Báo giá
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Tạp chí / Blog
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

// Bộ sưu tập (Portfolio)
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');

// Đặt lịch (Booking)
Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

/*
|--------------------------------------------------------------------------
| 2. Xác thực (Authentication Routes)
|--------------------------------------------------------------------------
*/

// Các route đăng nhập mặc định (thường do Laravel Breeze hoặc Filament quản lý)
// Nếu dùng Filament, bạn không cần khai báo lại vì Filament đã tự xử lý bảo mật.

/*
|--------------------------------------------------------------------------
| 3. Quản trị hệ thống (Admin Routes - Filament)
|--------------------------------------------------------------------------
| Phần này được bảo mật cực cao thông qua middleware 'auth' và 'can:access-admin'.
| Filament mặc định bảo mật tại đường dẫn /admin.
*/

// Lưu ý: Filament tự động tạo các route như /admin/posts, /admin/services...
// Bạn chỉ cần đảm bảo các Resource đã được đăng ký trong Filament.