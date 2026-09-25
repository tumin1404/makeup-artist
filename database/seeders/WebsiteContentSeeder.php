<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Service;
use App\Models\Post;
use App\Models\Portfolio;
use Carbon\Carbon;

class WebsiteContentSeeder extends Seeder
{
    /**
     * Helper download file từ URL an toàn
     */
    private function downloadMedia(string $url, string $destPath): bool
    {
        $dir = dirname($destPath);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 15,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $content = @file_get_contents($url, false, $context);
            if ($content !== false && strlen($content) > 500) {
                file_put_contents($destPath, $content);
                return true;
            }
        } catch (\Throwable $e) {
            // Không ngắt quá trình nếu tải lỗi
        }

        return false;
    }

    public function run(): void
    {
        // =========================================================================
        // 1. CẬP NHẬT THIẾT LẬP (SETTINGS): MXH, BẢN ĐỒ, LIÊN HỆ, QUY TRÌNH
        // =========================================================================
        $updatedSettings = [
            'process_step_3_desc' => 'Đến đúng giờ, chuẩn bị mỹ phẩm high-end chính hãng, bộ cọ vệ sinh sạch khuẩn và thực hiện makeup chuyên nghiệp tôn vinh vẻ đẹp riêng.',
            'social_facebook' => 'https://facebook.com/thaomakeup.luxury',
            'social_instagram' => 'https://instagram.com/thaomakeup_artist',
            'social_tiktok' => 'https://tiktok.com/@thaomakeup.vn',
            'social_zalo' => 'https://zalo.me/0912345678',
            'social_youtube' => 'https://youtube.com/@thaomakeupartist',
            'hotline' => '0912.345.678',
            'email' => 'thaomakeup.studio@gmail.com',
            'address_main' => 'Tầng 3, Toà Luxury Building, 102 Vũ Phạm Hàm, Cầu Giấy, Hà Nội',
            'address_branch' => 'Khu Đô Thị Ecopark, Văn Giang, Hưng Yên',
            'address' => 'Hà Nội & Các tỉnh thành lân cận (Nhận lưu động tận nơi)',
            'working_hours' => '07:30 - 21:00 (Tất cả các ngày trong tuần)',
            'map_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.113264426544!2d105.7951234!3d21.0281328!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4cd0c66f05%3A0x628045610850c95!2zMTAyIFbFqSBQaOG6oW0gSMOgbSwgWcOqbiBIw7JhLCBD4bqndSBHaeG6pXksIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2svn!4v1710000000000!5m2!1svi!2svn',
        ];

        foreach ($updatedSettings as $key => $val) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $val]
            );
        }

        Cache::forget('site_settings');

        // =========================================================================
        // 2. DANH MỤC & DỊCH VỤ (SERVICES)
        // =========================================================================
        Schema::disableForeignKeyConstraints();
        Service::truncate();
        Category::where('type', 'service')->delete();
        Schema::enableForeignKeyConstraints();

        $catBride = Category::create([
            'name' => 'Trang Điểm Cô Dâu',
            'slug' => 'trang-diem-co-dau',
            'type' => Category::TYPE_SERVICE,
            'order' => 1,
        ]);

        $catParty = Category::create([
            'name' => 'Trang Điểm Dự Tiệc',
            'slug' => 'trang-diem-du-tiec',
            'type' => Category::TYPE_SERVICE,
            'order' => 2,
        ]);

        $catConcept = Category::create([
            'name' => 'Trang Điểm Concept & Lookbook',
            'slug' => 'trang-diem-concept-lookbook',
            'type' => Category::TYPE_SERVICE,
            'order' => 3,
        ]);

        $catAcademy = Category::create([
            'name' => 'Đào Tạo Trang Điểm',
            'slug' => 'dao-tao-trang-diem',
            'type' => Category::TYPE_SERVICE,
            'order' => 4,
        ]);

        $servicesData = [
            [
                'category_id' => $catBride->id,
                'name' => 'Makeup Cô Dâu Ngày Cưới VIP',
                'price_text' => '4.500.000 VNĐ',
                'service_level' => 'Exclusive',
                'description' => 'Gói dịch vụ cao cấp nhất dành riêng cho cô dâu trong ngày trọng đại. Bao gồm lễ gia tiên hoặc tiệc cưới chính với sự chăm chút tỉ mỉ từng chi tiết.',
                'features' => [
                    'Sử dụng 100% mỹ phẩm High-End quốc tế (Dior, Tom Ford, Charlotte Tilbury, Chanel)',
                    'Tặng kèm hoa tươi cài tóc, vương miện & bộ trang sức cưới cao cấp',
                    'Dặm phấn, chỉnh sửa dặm lại lớp nền và thay đổi kiểu tóc giữa buổi tiệc',
                    'Miễn phí di chuyển nội thành Hà Nội',
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catBride->id,
                'name' => 'Makeup Cô Dâu Ăn Hỏi & Rước Dâu',
                'price_text' => '2.800.000 VNĐ',
                'service_level' => 'Signature',
                'description' => 'Layout trang điểm nhẹ nhàng, trong trẻo tôn vinh nét duyên dáng truyền thống khi diện Áo Dài cưới và váy ăn hỏi.',
                'features' => [
                    'Tone makeup Nude hồng / cam đào tươi tắn, thanh lịch',
                    'Làm tóc tạo kiểu bới sang trọng hoặc uốn sóng lãng mạn',
                    'Mi giả gân trong tự nhiên, không gây nặng cộm mắt',
                    'Tặng son dặm mini cao cấp cho cô dâu giữ màu suốt buổi lễ',
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catBride->id,
                'name' => 'Combo Cô Dâu & 2 Mẹ Cao Cấp',
                'price_text' => '6.000.000 VNĐ',
                'service_level' => 'VIP',
                'description' => 'Trọn gói làm đẹp hoàn hảo cho cả Cô Dâu, Mẹ Cô Dâu và Mẹ Chú Rể, đảm bảo cả gia đình cùng tỏa sáng đồng điệu.',
                'features' => [
                    '01 Gói Makeup Cô Dâu VIP ngày cưới trọn gói',
                    '02 Gói Makeup & Làm tóc sang trọng quý phái cho 2 Mẹ',
                    'Kỹ thuật nâng cơ & xử lý làn da tuổi trung niên giúp Mẹ trẻ trung hơn 10 tuổi',
                    'Tặng kèm hoa cài áo cho 2 Mẹ',
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catParty->id,
                'name' => 'Makeup Dự Tiệc & Gala Dinner',
                'price_text' => '1.500.000 VNĐ',
                'service_level' => 'Premium',
                'description' => 'Thiết kế phong cách trang điểm thời thượng phù hợp với từng dáng mặt và trang phục dạ hội, sự kiện công ty hay tiệc cưới bạn thân.',
                'features' => [
                    'Tư vấn layout makeup hợp trang phục và không gian tiệc',
                    'Làm tóc uốn xoăn sóng bồng bềnh hoặc búi tiệc quý phái',
                    'Khóa nền bền chặt suốt 12 tiếng không bóng dầu hay xuống tone',
                    'Tạo khối 3D thon gọn khuôn mặt và bắt sáng kiêu sa',
                ],
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catConcept->id,
                'name' => 'Makeup Chụp Ảnh Lookbook & Fashion',
                'price_text' => '2.000.000 VNĐ',
                'service_level' => 'Professional',
                'description' => 'Dịch vụ trang điểm chuyên nghiệp cho người mẫu, lookbook thời trang, chụp ảnh cá nhân nghệ thuật hoặc profile doanh nhân.',
                'features' => [
                    'Makeup chuẩn ánh sáng đèn studio và ngoại cảnh',
                    'Hỗ trợ đi kèm dặm phấn & đổi kiểu tóc suốt buổi chụp (2 - 3 tiếng)',
                    'Đổi layout linh hoạt theo từng set trang phục',
                    'Kỹ thuật xử lý da căng bóng chuẩn lookbook tạp chí',
                ],
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'category_id' => $catAcademy->id,
                'name' => 'Khóa Học Makeup Cá Nhân Siêu Tốc (1:1)',
                'price_text' => '3.500.000 VNĐ',
                'service_level' => 'Academy',
                'description' => 'Khóa học 05 buổi kèm 1:1 trực tiếp cùng chuyên gia giúp bạn tự tin làm chủ gương mặt và tự trang điểm đẹp mọi lúc mọi nơi.',
                'features' => [
                    '05 buổi học 1 kèm 1 theo thời gian linh hoạt của học viên',
                    'Hướng dẫn phân tích da, dáng mặt và định hình phong cách cá nhân',
                    'Tài trợ 100% mỹ phẩm và cọ cao cấp trong suốt quá trình học',
                    'Cấp chứng nhận hoàn thành và tư vấn bộ mỹ phẩm cá nhân chuẩn nhất',
                ],
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($servicesData as $s) {
            Service::create($s);
        }

        // =========================================================================
        // 3. DANH MỤC & 6 BÀI VIẾT TẠP CHÍ (POSTS)
        // =========================================================================
        Schema::disableForeignKeyConstraints();
        Post::truncate();
        Category::where('type', 'post')->delete();
        Schema::enableForeignKeyConstraints();

        $catTrend = Category::create([
            'name' => 'Xu Hướng Makeup',
            'slug' => 'xu-huong-makeup',
            'type' => Category::TYPE_POST,
            'order' => 1,
        ]);

        $catBrideGuide = Category::create([
            'name' => 'Cẩm Nang Cô Dâu',
            'slug' => 'cam-nang-co-dau',
            'type' => Category::TYPE_POST,
            'order' => 2,
        ]);

        $catBeautyTips = Category::create([
            'name' => 'Bí Quyết Làm Đẹp',
            'slug' => 'bi-quyet-lam-dep',
            'type' => Category::TYPE_POST,
            'order' => 3,
        ]);

        // Danh sách ảnh Unsplash cho bài viết
        $postImages = [
            1 => [
                'thumb' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1200&q=85',
                'content' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=1000&q=85',
            ],
            2 => [
                'thumb' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1200&q=85',
                'content' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1000&q=85',
            ],
            3 => [
                'thumb' => 'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?auto=format&fit=crop&w=1200&q=85',
                'content' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=1000&q=85',
            ],
            4 => [
                'thumb' => 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?auto=format&fit=crop&w=1200&q=85',
                'content' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=1000&q=85',
            ],
            5 => [
                'thumb' => 'https://images.unsplash.com/photo-1588510849445-4795b27782fd?auto=format&fit=crop&w=1200&q=85',
                'content' => 'https://images.unsplash.com/photo-1526045612212-70caf35c14df?auto=format&fit=crop&w=1000&q=85',
            ],
            6 => [
                'thumb' => 'https://images.unsplash.com/photo-1595476108010-b4d1f102b1b1?auto=format&fit=crop&w=1200&q=85',
                'content' => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=1000&q=85',
            ],
        ];

        // Tải ảnh vào storage
        $savedPostThumbs = [];
        $savedPostContents = [];
        foreach ($postImages as $idx => $imgs) {
            $thumbFile = "posts/thumbnails/post_thumb_{$idx}.jpg";
            $contentFile = "posts/content/post_content_{$idx}.jpg";

            $this->downloadMedia($imgs['thumb'], storage_path('app/public/' . $thumbFile));
            $this->downloadMedia($imgs['content'], storage_path('app/public/' . $contentFile));

            $savedPostThumbs[$idx] = $thumbFile;
            $savedPostContents[$idx] = $contentFile;
        }

        $postsData = [
            [
                'category_id' => $catTrend->id,
                'title' => 'Top 5 Xu Hướng Trang Điểm Cô Dâu Thống Trị Năm 2026: Nude Luxury Lên Ngôi',
                'slug' => 'top-5-xu-huong-trang-diem-co-dau-2026',
                'excerpt' => 'Khám phá những phong cách makeup cưới dẫn đầu xu hướng năm 2026, từ vẻ đẹp trong suốt mỏng nhẹ đến layout Nude Tây sang trọng đầy cuốn hút.',
                'thumbnail' => $savedPostThumbs[1],
                'content' => '
                    <p class="lead">Năm 2026 đánh dấu sự chuyển mình mạnh mẽ trong phong cách trang điểm cô dâu: tạm biệt những lớp phấn dày cộm, nặng nề để nhường chỗ cho <strong>vẻ đẹp nguyên bản được tôn vinh một cách tinh tế (Quiet Luxury Makeup)</strong>.</p>
                    
                    <h3>1. Phong Cách Nude Luxury – Sự Sang Trọng Thuần Khiết</h3>
                    <p>Tone Nude không hề làm cô dâu bị nhạt nhòa, mà ngược lại mang đến chiều sâu quý phái khó cưỡng. Sự kết hợp giữa mắt tone nâu khói nhạt, đường eyeliner gân trong và màu son nude hồng đất tạo nên thần thái của một tiểu thư đài các.</p>
                    
                    <p><img src="' . asset('storage/' . $savedPostContents[1]) . '" alt="Makeup cô dâu Nude Luxury" class="rounded-2xl shadow-lg my-6 w-full" /></p>
                    
                    <h3>2. Lớp Nền Thủy Tinh (Glass Skin) Mỏng Nhẹ</h3>
                    <p>Khả năng bắt sáng tự nhiên dưới ánh đèn lễ đường là ưu tiên hàng đầu. Chuyên gia sử dụng kỹ thuật dưỡng ẩm nhiều tầng và kem nền gốc nước (water-based) kết hợp phấn phủ vi hạt để giữ lớp nền trong suốt suốt 12 tiếng.</p>
                    
                    <h3>3. Hàng Mi Tơi Mềm Tự Nhiên</h3>
                    <p>Mi nối hay mi giả từng chùm dày đã được thay thế hoàn toàn bằng kỹ thuật <em>dán mi gân trong từng sợi</em>, mang lại ánh nhìn tự nhiên, long lanh nhưng không hề gây cảm giác nặng mí.</p>
                ',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'category_id' => $catBrideGuide->id,
                'title' => 'Quy Trình Skincare 7 Ngày Trước Ngày Cưới Để Lớp Nền "Ăn Tiệp" Hoàn Hảo',
                'slug' => 'quy-trinh-skincare-7-ngay-truoc-ngay-cuoi',
                'excerpt' => 'Bí quyết cấp ẩm sâu, phục hồi da và lịch trình chăm sóc da chuẩn chuyên gia giúp bạn có làn da căng bóng, sẵn sàng cho ngày trọng đại.',
                'thumbnail' => $savedPostThumbs[2],
                'content' => '
                    <p>Một lớp makeup hoàn mỹ 70% quyết định bởi nền da thật bên dưới. Dưới đây là lịch trình vàng 7 ngày chăm sóc da dành riêng cho các nàng dâu tương lai.</p>
                    
                    <h3>Ngày 7 - 5: Làm Sạch Sâu & Cấp Nước Tầng Sâu</h3>
                    <p>Tẩy da chết hóa học dịu nhẹ (AHA/PHA nồng độ thấp) để loại bỏ lớp sừng cằn cỗi. Tăng cường đắp mặt nạ cấp ẩm chứa Hyaluronic Acid và Ceramide để củng cố hàng rào bảo vệ da.</p>
                    
                    <p><img src="' . asset('storage/' . $savedPostContents[2]) . '" alt="Quy trình chăm sóc da cô dâu" class="rounded-2xl shadow-lg my-6 w-full" /></p>
                    
                    <h3>Ngày 4 - 2: Phục Hồi & Tuyệt Đối Không Thử Mỹ Phẩm Mới</h3>
                    <p>Đây là giai đoạn nhạy cảm, tuyệt đối không nặn mụn, peel da hay đổi mỹ phẩm mới. Hãy tập trung dưỡng ẩm bằng kem khóa ẩm lành tính và uống đủ 2.5 lít nước mỗi ngày.</p>
                    
                    <h3>Ngày 1: Thư Giãn & Dưỡng Ẩm Môi</h3>
                    <p>Tẩy tế bào chết môi nhẹ nhàng bằng đường nâu hoặc son dưỡng đặc trị. Ngủ đủ 8 tiếng để hạn chế quầng thâm và bọng mắt trước giờ G.</p>
                ',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'category_id' => $catBeautyTips->id,
                'title' => 'Cách Xác Định Sắc Tố Da (Undertone) Để Chọn Màu Son & Phấn Má Tôn Da Nhất',
                'slug' => 'cach-xac-dinh-undertone-chon-mau-son',
                'excerpt' => 'Bạn thuộc Warm, Cool hay Neutral Undertone? Khám phá mẹo nhận biết sắc tố da chuẩn xác và bí quyết lựa chọn tone makeup tôn trọn thần thái.',
                'thumbnail' => $savedPostThumbs[3],
                'content' => '
                    <p>Rất nhiều bạn nữ mua những thỏi son hay màu phấn má rất hot nhưng khi đánh lên mặt lại thấy xỉn da. Lý do lớn nhất là bạn chưa chọn đúng màu theo <strong>Undertone (Sắc tố da ngầm)</strong>.</p>
                    
                    <h3>1. Cách Kiểm Tra Mạch Máu Ở Cổ Tay Dưới Ánh Sáng Tự Nhiên</h3>
                    <ul>
                        <li><strong>Mạch máu xanh lá:</strong> Bạn thuộc <em>Warm Undertone (Tone ấm)</em>. Thích hợp với son cam đất, đỏ gạch, san hô, phấn má tone đào và highlight vàng gold.</li>
                        <li><strong>Mạch máu xanh dương / tím:</strong> Bạn thuộc <em>Cool Undertone (Tone lạnh)</em>. Rất hợp với son hồng cánh sen, đỏ rượu, đỏ cherry và phấn má hồng baby.</li>
                        <li><strong>Có cả xanh lá lẫn xanh dương:</strong> Bạn thuộc <em>Neutral Undertone (Tone trung tính)</em> – tone da lý tưởng có thể diện đẹp mọi màu son!</li>
                    </ul>
                    
                    <p><img src="' . asset('storage/' . $savedPostContents[3]) . '" alt="Bảng màu mỹ phẩm theo tone da" class="rounded-2xl shadow-lg my-6 w-full" /></p>
                ',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(8),
            ],
            [
                'category_id' => $catBeautyTips->id,
                'title' => 'Nghệ Thuật Đánh Nền Căng Bóng Bền Màu 16 Tiếng Không Mốc (Cakey)',
                'slug' => 'nghe-thuat-danh-nen-cang-bong-khong-moc',
                'excerpt' => 'Hướng dẫn kỹ thuật triệt sắc vi điểm, dưỡng ẩm trước nền và kỹ thuật dặm mút ẩm giúp lớp trang điểm mịn màng suốt cả ngày dài.',
                'thumbnail' => $savedPostThumbs[4],
                'content' => '
                    <p>Hiện tượng mốc nền (cakey) hay vỡ nền quanh cánh mũi, khóe cười là nỗi ám ảnh của phái đẹp khi trang điểm. Hãy áp dụng 4 nguyên tắc sau để có lớp nền mượt mà như nhung:</p>
                    
                    <h3>Nguyên Tắc 1: Cấp Ẩm Đúng Cách Trước Khi Đánh Nền</h3>
                    <p>Đợi kem dưỡng ẩm và kem lót thẩm thấu hoàn toàn vào da ít nhất 3 phút trước khi dặm kem nền. Dùng kem lót kiểm soát dầu ở vùng chữ T và kem lót cấp ẩm ở hai bên má.</p>
                    
                    <p><img src="' . asset('storage/' . $savedPostContents[4]) . '" alt="Lớp nền hoàn hảo" class="rounded-2xl shadow-lg my-6 w-full" /></p>
                    
                    <h3>Nguyên Tắc 2: Đánh Nền Từng Lớp Mỏng Bằng Mút Ẩm</h3>
                    <p>Lấy một lượng nền nhỏ, dùng mút đã xịt khoáng ẩm dặm vuông góc vào da thay vì miết ngang. Kỹ thuật này giúp hạt phấn nén chặt vào lỗ chân lông mà không gây bí bách.</p>
                ',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(12),
            ],
            [
                'category_id' => $catTrend->id,
                'title' => 'Trang Điểm Dự Tiệc Đêm: Bí Quyết Đôi Mắt Khói Quyến Rũ Mọi Ánh Nhìn',
                'slug' => 'trang-diem-du-tiec-dem-mat-khoi-quyen-ru',
                'excerpt' => 'Biến hóa phong cách kiêu sa, quyến rũ cho đêm tiệc với layout mắt khói hiện đại kết hợp nhũ mắt kim cương bắt sáng lộng lẫy.',
                'thumbnail' => $savedPostThumbs[5],
                'content' => '
                    <p>Đôi mắt là linh hồn của gương mặt. Trong các sự kiện dạ hội và tiệc tối, phong cách mắt khói hiện đại (Modern Smokey Eyes) luôn là sự lựa chọn số 1 của các quý cô sành điệu.</p>
                    
                    <h3>Kỹ Thuật Tán Mắt Khói Mềm Mại</h3>
                    <p>Không dùng màu đen đậm ngay từ đầu, chuyên gia makeup sẽ xây dựng các lớp chuyển màu từ nâu caramel, nâu chocolate đến đen khói ở đuôi mắt để tạo độ sâu thăm thẳm.</p>
                    
                    <p><img src="' . asset('storage/' . $savedPostContents[5]) . '" alt="Trang điểm mắt khói dạ tiệc" class="rounded-2xl shadow-lg my-6 w-full" /></p>
                    
                    <h3>Điểm Nhấn Nhũ Bắt Sáng Kim Cương</h3>
                    <p>Một chút phấn nhũ ngọc trai ở giữa bầu mắt và đầu khóe mắt sẽ bắt trọn ánh đèn pha lê của buổi tiệc, giúp đôi mắt bạn tỏa sáng lấp lánh như những vì sao.</p>
                ',
                'is_featured' => false,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(16),
            ],
            [
                'category_id' => $catBrideGuide->id,
                'title' => 'Những Lưu Ý "Vàng" Khi Chọn Kiểu Tóc & Phụ Kiện Cưới Phù Hợp Với Khuôn Mặt',
                'slug' => 'chon-kieu-toc-phu-kien-cuoi-phu-hop',
                'excerpt' => 'Sự kết hợp hoàn hảo giữa kiểu tóc uốn sóng, búi cao đài các và phụ kiện cài tóc giúp cô dâu hoàn thiện diện mạo rạng ngời nhất.',
                'thumbnail' => $savedPostThumbs[6],
                'content' => '
                    <p>Mái tóc và phụ kiện cài đầu chính là chiếc vương miện tôn vinh nét đẹp của nàng dâu trong hôn lễ.</p>
                    
                    <h3>1. Mặt Tròn: Ưu Tiên Kiểu Tóc Búi Cao Đánh Phồng Đỉnh Đầu</h3>
                    <p>Kiểu tóc búi cao kết hợp vài lọn tóc mai uốn lơi nhẹ nhàng ôm lấy hai bên má sẽ giúp gương mặt thon gọn và thanh thoát hơn rất nhiều.</p>
                    
                    <p><img src="' . asset('storage/' . $savedPostContents[6]) . '" alt="Kiểu tóc và phụ kiện cưới" class="rounded-2xl shadow-lg my-6 w-full" /></p>
                    
                    <h3>2. Phụ Kiện Cài Tóc: Tối Giản Là Đẳng Cấp</h3>
                    <p>Nếu váy cưới của bạn đã đính kết nhiều pha lê, hãy chọn trâm cài hoa ngọc trai nhỏ nhắn. Ngược lại, với những chiếc váy lụa satin trơn tối giản, một chiếc vương miện đính đá vương giả sẽ là điểm nhấn hoàn hảo.</p>
                ',
                'is_featured' => true,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(20),
            ],
        ];

        foreach ($postsData as $p) {
            Post::create($p);
        }

        // =========================================================================
        // 4. DANH MỤC & 20 TÀI NGUYÊN BỘ SƯU TẬP (PORTFOLIOS: ẢNH DỌC/NGANG & VIDEO)
        // =========================================================================
        Schema::disableForeignKeyConstraints();
        Portfolio::truncate();
        Category::where('type', 'portfolio')->delete();
        Schema::enableForeignKeyConstraints();

        $pCatBride = Category::create([
            'name' => 'Cô Dâu Ngày Cưới',
            'slug' => 'co-dau-ngay-cuoi',
            'type' => Category::TYPE_PORTFOLIO,
            'order' => 1,
        ]);

        $pCatParty = Category::create([
            'name' => 'Dự Tiệc & Dạ Hội',
            'slug' => 'du-tiec-da-hoi',
            'type' => Category::TYPE_PORTFOLIO,
            'order' => 2,
        ]);

        $pCatConcept = Category::create([
            'name' => 'Concept Nghệ Thuật',
            'slug' => 'concept-nghe-thuat',
            'type' => Category::TYPE_PORTFOLIO,
            'order' => 3,
        ]);

        $pCatLookbook = Category::create([
            'name' => 'Lookbook Thời Trang',
            'slug' => 'lookbook-thoi-trang',
            'type' => Category::TYPE_PORTFOLIO,
            'order' => 4,
        ]);

        $pCatVideo = Category::create([
            'name' => 'Video Biến Hình & Hậu Trường',
            'slug' => 'video-bien-hinh',
            'type' => Category::TYPE_PORTFOLIO,
            'order' => 5,
        ]);

        // Danh sách 16 ảnh chất lượng cao (dọc & ngang) từ Unsplash
        $portfolioPhotos = [
            1 => ['url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1000&q=85', 'cat' => $pCatBride->id, 'title' => 'Cô Dâu Nude Luxury – Lễ Đường Trang Trọng'],
            2 => ['url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatBride->id, 'title' => 'Cận Cảnh Nét Đẹp Cô Dâu Trong Suốt'],
            3 => ['url' => 'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatBride->id, 'title' => 'Cô Dâu Á Đông Dịu Dàng Ngày Ăn Hỏi'],
            4 => ['url' => 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=1200&q=85', 'cat' => $pCatBride->id, 'title' => 'Khoảnh Khắc Đội Voan Cưới Bồng Bềnh'],
            5 => ['url' => 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?auto=format&fit=crop&w=1200&q=85', 'cat' => $pCatBride->id, 'title' => 'Pre-Wedding Ngoại Cảnh Hoàng Hôn'],
            
            6 => ['url' => 'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatParty->id, 'title' => 'Layout Dạ Tiệc Sang Trọng Mắt Khói'],
            7 => ['url' => 'https://images.unsplash.com/photo-1588510849445-4795b27782fd?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatParty->id, 'title' => 'Son Đỏ Cổ Điển Quyến Rũ Gala Dinner'],
            8 => ['url' => 'https://images.unsplash.com/photo-1526045612212-70caf35c14df?auto=format&fit=crop&w=1200&q=85', 'cat' => $pCatParty->id, 'title' => 'Mắt Nhũ Kim Cương Đi Tiệc Tối'],
            9 => ['url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatParty->id, 'title' => 'Trang Điểm Sinh Nhật & Tiệc Bạn Thân'],

            10 => ['url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatConcept->id, 'title' => 'Concept Nàng Thơ Ánh Sáng Tự Nhiên'],
            11 => ['url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatConcept->id, 'title' => 'Chân Dung Nghệ Thuật High-Fashion'],
            12 => ['url' => 'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatConcept->id, 'title' => 'Beauty Glow – Làn Da Thủy Tinh'],
            13 => ['url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatConcept->id, 'title' => 'Clean Girl Nude Độc Bản'],

            14 => ['url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatLookbook->id, 'title' => 'Lookbook BST Váy Cưới Mùa Thu'],
            15 => ['url' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?auto=format&fit=crop&w=800&h=1200&q=85', 'cat' => $pCatLookbook->id, 'title' => 'Chụp Profile Doanh Nhân & MC'],
            16 => ['url' => 'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?auto=format&fit=crop&w=1200&q=85', 'cat' => $pCatLookbook->id, 'title' => 'BST Thời Trang Nữ Hiện Đại'],
        ];

        $sortOrder = 1;
        foreach ($portfolioPhotos as $id => $item) {
            $fileName = "portfolios/portfolio_img_{$id}.jpg";
            $this->downloadMedia($item['url'], storage_path('app/public/' . $fileName));

            Portfolio::create([
                'title' => $item['title'],
                'category_id' => $item['cat'],
                'type' => 'image',
                'file_path' => $fileName,
                'poster_path' => null,
                'is_active' => true,
                'sort_order' => $sortOrder++,
            ]);
        }

        // 4 Video clip mẫu trong danh mục "Video Biến Hình & Hậu Trường"
        // Kiểm tra xem có video file sẵn trong storage không
        $existingVideos = File::glob(storage_path('app/public/portfolios/*.mp4'));

        $videoTitles = [
            'Hậu Trường Trang Điểm Cô Dâu Trước Giờ G',
            'Biến Hình Trước & Sau Khi Makeup (Magic Transformation)',
            'Slow-motion Cận Cảnh Đôi Mắt Khói Lấp Lánh',
            'Toàn Cảnh Cô Dâu Tỏa Sáng Dưới Ánh Đèn Tiệc Cưới',
        ];

        $posterUrls = [
            'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=600&h=900&q=80',
            'https://images.unsplash.com/photo-1487412947147-5cebf100ffc2?auto=format&fit=crop&w=600&h=900&q=80',
            'https://images.unsplash.com/photo-1588510849445-4795b27782fd?auto=format&fit=crop&w=600&h=900&q=80',
            'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=600&h=900&q=80',
        ];

        for ($v = 0; $v < 4; $v++) {
            $videoPath = null;
            if (!empty($existingVideos[$v])) {
                $videoPath = 'portfolios/' . basename($existingVideos[$v]);
            } else if (!empty($existingVideos[0])) {
                $videoPath = 'portfolios/' . basename($existingVideos[0]);
            }

            $posterPath = "portfolios/portfolio_video_poster_{$v}.jpg";
            $this->downloadMedia($posterUrls[$v], storage_path('app/public/' . $posterPath));

            if ($videoPath) {
                Portfolio::create([
                    'title' => $videoTitles[$v],
                    'category_id' => $pCatVideo->id,
                    'type' => 'video',
                    'file_path' => $videoPath,
                    'poster_path' => $posterPath,
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }
    }
}
