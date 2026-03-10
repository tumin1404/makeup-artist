<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Service;
use App\Models\Post;
use App\Models\Portfolio;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo tài khoản Admin mặc định
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Tú Mindx', 'password' => bcrypt('123456')]
        );

        // 2. Bảng Settings (Giữ nguyên)
        // Lưu ý: Nếu bạn chưa có bảng settings thì hãy comment đoạn này lại để tránh lỗi
        Setting::insert([
            ['key' => 'site_name', 'value' => 'Thảo Makeup Artist', 'group' => 'general', 'type' => 'text'],
            ['key' => 'hotline', 'value' => '0912.345.678', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'address', 'value' => 'Hưng Yên, Việt Nam', 'group' => 'contact', 'type' => 'text'],
        ]);

        // 3. Bảng Banners (Giữ nguyên)
        // Lưu ý: Nếu chưa có bảng banners thì comment lại
        Banner::insert([
            ['title' => 'BST Cô Dâu Mùa Xuân 2026', 'image_path' => 'banner1.jpg', 'position' => 'home_hero', 'is_active' => true],
            ['title' => 'Ưu đãi Makeup Tiệc Nhóm', 'image_path' => 'banner2.jpg', 'position' => 'home_hero', 'is_active' => true],
            ['title' => 'Khóa học Cá nhân Cấp tốc', 'image_path' => 'banner3.jpg', 'position' => 'popup', 'is_active' => true],
        ]);

        // 4. Bảng Categories (CHỈ DÙNG CHO BÀI VIẾT)
        $catPost = Category::create(['name' => 'Mẹo làm đẹp', 'slug' => 'meo-lam-dep']);
        $catTrend = Category::create(['name' => 'Xu hướng Bridal', 'slug' => 'xu-huong-bridal']);

        // 5. Bảng Services (Đã gỡ bỏ category_id)
        $s1 = Service::create([
            'name' => 'Makeup Cô Dâu (Lễ ăn hỏi)',
            'price_text' => '1.500.000 VNĐ',
            'features' => json_encode(['Tặng làm tóc', 'Dặm phấn tại nhà', 'Mỹ phẩm High-end']),
            'is_active' => true
        ]);
        $s2 = Service::create([
            'name' => 'Makeup Tiệc Tối / Sự kiện',
            'price_text' => '500.000 VNĐ',
            'features' => json_encode(['Phong cách Douyin', 'Gắn mi giả tự nhiên']),
            'is_active' => true
        ]);
        $s3 = Service::create([
            'name' => 'Khóa học Makeup cá nhân',
            'price_text' => '2.000.000 VNĐ',
            'features' => json_encode(['3 buổi học', 'Hỗ trợ cốp đồ', 'Tặng bộ cọ']),
            'is_active' => true
        ]);

        // 6. Bảng Posts (Bổ sung category_id)
        for ($i = 1; $i <= 3; $i++) {
            Post::create([
                'category_id' => $catPost->id, // Ràng buộc bài viết vào danh mục
                'title' => "Bí quyết giữ lớp nền lâu trôi số $i",
                'slug' => "bi-quyet-giu-nen-$i",
                'excerpt' => "Hướng dẫn chi tiết cách đánh nền cho da dầu...",
                'content' => "<p>Nội dung bài viết chia sẻ kinh nghiệm makeup thực tế của Thảo...</p>",
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        // 7. Bảng Portfolios (Cập nhật cột file_path và category dạng string)
        Portfolio::insert([
            ['title' => 'Cô dâu rạng rỡ', 'category' => 'bride', 'type' => 'image', 'file_path' => 'p1.jpg', 'is_active' => true],
            ['title' => 'Makeup kỷ yếu', 'category' => 'personal', 'type' => 'image', 'file_path' => 'p2.jpg', 'is_active' => true],
            ['title' => 'Đi tiệc cuối năm', 'category' => 'event', 'type' => 'image', 'file_path' => 'p3.jpg', 'is_active' => true],
        ]);

        // 8. Bảng Bookings (Giữ nguyên logic)
        // Lưu ý: Nếu chưa có bảng bookings thì comment lại
        $names = ['Phương Ly', 'Thu Hà', 'Mai Anh', 'Bích Ngọc', 'Tuyết Nhung'];
        $statuses = ['pending', 'confirmed', 'completed', 'canceled'];

        foreach ($names as $key => $name) {
            Booking::create([
                'customer_name' => "Nguyễn $name",
                'phone' => '09' . rand(10000000, 99999999),
                'service_id' => ($key % 2 == 0) ? $s1->id : $s2->id,
                'booking_date' => now()->addDays(rand(1, 20))->addHours(rand(8, 18)),
                'message' => "Khách hàng book lịch vào ngày " . now()->addDays(rand(1, 5))->format('d/m'),
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}