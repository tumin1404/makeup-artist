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
        // 1. Tạo tài khoản Admin mặc định (để không phải tạo lại thủ công)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Hiền Anh Admin', 'password' => bcrypt('123456')]
        );

        // 2. Bảng Settings (3 mẫu)
        Setting::insert([
            ['key' => 'site_name', 'value' => 'Thảo Makeup Artist', 'group' => 'general', 'type' => 'text'],
            ['key' => 'hotline', 'value' => '0912.345.678', 'group' => 'contact', 'type' => 'text'],
            ['key' => 'address', 'value' => 'Hưng Yên, Việt Nam', 'group' => 'contact', 'type' => 'text'],
        ]);

        // 3. Bảng Banners (3 mẫu)
        Banner::insert([
            ['title' => 'BST Cô Dâu Mùa Xuân 2026', 'image_path' => 'banner1.jpg', 'position' => 'home_hero', 'is_active' => true],
            ['title' => 'Ưu đãi Makeup Tiệc Nhóm', 'image_path' => 'banner2.jpg', 'position' => 'home_hero', 'is_active' => true],
            ['title' => 'Khóa học Cá nhân Cấp tốc', 'image_path' => 'banner3.jpg', 'position' => 'popup', 'is_active' => true],
        ]);

        // 4. Bảng Categories (3 mẫu)
        $catService = Category::create(['name' => 'Dịch vụ Makeup', 'slug' => 'dich-vu-makeup', 'type' => 'service']);
        $catPortfolio = Category::create(['name' => 'Ảnh Cô Dâu', 'slug' => 'anh-co-dau', 'type' => 'portfolio']);
        $catPost = Category::create(['name' => 'Mẹo làm đẹp', 'slug' => 'meo-lam-dep', 'type' => 'post']);

        // 5. Bảng Services (3 mẫu)
        $s1 = Service::create([
            'category_id' => $catService->id,
            'name' => 'Makeup Cô Dâu (Lễ ăn hỏi)',
            'price_text' => '1.500.000 VNĐ',
            'features' => json_encode(['Tặng làm tóc', 'Dặm phấn tại nhà', 'Mỹ phẩm High-end']),
            'is_active' => true
        ]);
        $s2 = Service::create([
            'category_id' => $catService->id,
            'name' => 'Makeup Tiệc Tối / Sự kiện',
            'price_text' => '500.000 VNĐ',
            'features' => json_encode(['Phong cách Douyin', 'Gắn mi giả tự nhiên']),
            'is_active' => true
        ]);
        $s3 = Service::create([
            'category_id' => $catService->id,
            'name' => 'Khóa học Makeup cá nhân',
            'price_text' => '2.000.000 VNĐ',
            'features' => json_encode(['3 buổi học', 'Hỗ trợ cốp đồ', 'Tặng bộ cọ']),
            'is_active' => true
        ]);

        // 6. Bảng Posts (3 mẫu)
        for ($i = 1; $i <= 3; $i++) {
            Post::create([
                'title' => "Bí quyết giữ lớp nền lâu trôi số $i",
                'slug' => "bi-quyet-giu-nen-$i",
                'excerpt' => "Hướng dẫn chi tiết cách đánh nền cho da dầu...",
                'content' => "Nội dung bài viết chia sẻ kinh nghiệm makeup thực tế của Thảo...",
                'status' => 'published',
                'published_at' => now(),
            ]);
        }

        // 7. Bảng Portfolios (3 mẫu)
        Portfolio::insert([
            ['category_id' => $catPortfolio->id, 'image_path' => 'p1.jpg', 'layout_type' => 'portrait'],
            ['category_id' => $catPortfolio->id, 'image_path' => 'p2.jpg', 'layout_type' => 'landscape'],
            ['category_id' => $catPortfolio->id, 'image_path' => 'p3.jpg', 'layout_type' => 'square'],
        ]);

        // 8. Bảng Bookings (10 mẫu)
        $names = ['Phương Ly', 'Thu Hà', 'Mai Anh', 'Bích Ngọc', 'Tuyết Nhung', 'Thùy Chi', 'Khánh Linh', 'Minh Vũ', 'Thanh Trà', 'Hương Giang'];
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