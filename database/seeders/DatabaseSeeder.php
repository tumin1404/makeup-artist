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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo tài khoản Admin mặc định (Hỗ trợ cấu hình qua file .env)
        $adminEmail = env('ADMIN_DEFAULT_EMAIL', 'admin@example.com');
        $adminName = env('ADMIN_DEFAULT_NAME', 'Quản trị viên');
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD', 'Admin@123456');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => bcrypt($adminPassword),
                'email_verified_at' => now(),
            ]
        );

        // 2. Bảng Settings chuẩn hóa 100% thương mại hóa & Khuyến nghị chi tiết cho từng Element
        $standardSettings = [
            // --- NHÓM 1: CẤU HÌNH CHUNG, LOGO & SEO (general) ---
            [
                'group' => 'general',
                'key' => 'site_name',
                'description' => 'Tên thương hiệu / Studio / Makeup Artist',
                'recommendation' => 'Tên thương hiệu ngắn gọn, sang trọng (2 - 5 từ), hiển thị trên thanh điều hướng và tiêu đề tab trình duyệt.',
                'value' => 'Thảo Makeup Artist',
                'type' => 'text'
            ],
            [
                'group' => 'general',
                'key' => 'site_tagline',
                'description' => 'Slogan / Khẩu hiệu thương hiệu',
                'recommendation' => 'Khẩu hiệu ngắn gọn định vị phong cách nghệ thuật và giá trị cốt lõi của thương hiệu.',
                'value' => 'Nghệ thuật tôn vinh vẻ đẹp nguyên bản',
                'type' => 'text'
            ],
            [
                'group' => 'general',
                'key' => 'site_logo',
                'description' => 'Logo chính của thương hiệu (Header & Hóa đơn)',
                'recommendation' => 'Khuyến nghị: Định dạng PNG hoặc SVG nền trong suốt, kích thước chuẩn 400x120px (tỷ lệ 3:1 hoặc 4:1), dung lượng < 500KB để tốc độ tải trang nhanh nhất.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'general',
                'key' => 'site_favicon',
                'description' => 'Biểu tượng website trên thanh tab trình duyệt (Favicon)',
                'recommendation' => 'Khuyến nghị: Ảnh hình vuông định dạng .ico hoặc .png, kích thước chuẩn 64x64px hoặc 32x32px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'general',
                'key' => 'site_meta_image',
                'description' => 'Ảnh đại diện khi chia sẻ website lên MXH (Facebook, Zalo, OpenGraph)',
                'recommendation' => 'Khuyến nghị: Ảnh chất lượng cao tỷ lệ 16:9, kích thước chuẩn 1200x630px, dung lượng < 1MB.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'general',
                'key' => 'site_description',
                'description' => 'Mô tả tóm tắt SEO website (Meta Description)',
                'recommendation' => 'Độ dài khuyến nghị: 120 - 160 ký tự, tóm tắt dịch vụ trang điểm cô dâu, sự kiện nổi bật và địa bàn hoạt động để đạt thứ hạng cao trên Google.',
                'value' => 'Dịch vụ trang điểm chuyên nghiệp cho cô dâu, sự kiện, nghệ thuật với phong cách sang trọng, tinh tế.',
                'type' => 'textarea'
            ],
            [
                'group' => 'general',
                'key' => 'site_keywords',
                'description' => 'Từ khóa tìm kiếm SEO Google (Meta Keywords)',
                'recommendation' => 'Nhập các cụm từ khóa tìm kiếm cách nhau bởi dấu phẩy (Ví dụ: makeup artist, trang điểm cô dâu, makeup đi tiệc, nude luxury).',
                'value' => 'makeup artist, trang điểm cô dâu, makeup đi tiệc, trang điểm nghệ thuật, makeup chuyên nghiệp',
                'type' => 'text'
            ],

            // --- NHÓM 2: THÔNG TIN LIÊN HỆ & ĐỊA CHỈ (contact) ---
            [
                'group' => 'contact',
                'key' => 'hotline',
                'description' => 'Số điện thoại Hotline chính / CSKH',
                'recommendation' => 'Số điện thoại liên hệ chính thức, định dạng số dễ nhìn (VD: 0912.345.678).',
                'value' => '0912.345.678',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'email',
                'description' => 'Địa chỉ Email liên hệ & CSKH',
                'recommendation' => 'Địa chỉ hòm thư điện tử nhận thông tin liên hệ và phản hồi khách hàng.',
                'value' => 'contact@makeupartist.vn',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'address_main',
                'description' => 'Địa chỉ Cơ sở 1 (Trụ sở chính / Studio)',
                'recommendation' => 'Địa chỉ studio làm việc chính thức để khách hàng đến trải nghiệm dịch vụ trực tiếp.',
                'value' => 'Studio Nude Luxury, Q. Cầu Giấy, Hà Nội',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'address_branch',
                'description' => 'Địa chỉ Cơ sở 2 (Chi nhánh / Workshop)',
                'recommendation' => 'Địa chỉ cơ sở thứ 2 hoặc khu vực phục vụ mở rộng (nếu có).',
                'value' => 'TP. Hưng Yên & Các khu vực lân cận',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'address',
                'description' => 'Địa chỉ bổ sung / Ghi chú vùng phục vụ',
                'recommendation' => 'Địa chỉ phụ hoặc ghi chú phạm vi phục vụ lưu động.',
                'value' => 'Hà Nội & Các tỉnh thành phía Bắc',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'working_hours',
                'description' => 'Thời gian mở cửa & Phục vụ khách hàng',
                'recommendation' => 'Khung giờ phục vụ hàng ngày (Ví dụ: 08:00 - 20:00 (Tất cả các ngày trong tuần)).',
                'value' => '08:00 - 20:00 (Tất cả các ngày trong tuần)',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'map_iframe',
                'description' => 'Đường dẫn nhúng bản đồ Google Maps',
                'recommendation' => 'Khuyến nghị: Lấy đường dẫn trong thuộc tính src của mã nhúng Google Maps iframe (Ví dụ: https://www.google.com/maps/embed?...).',
                'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.8638558814!2d105.7891868!3d21.0381328!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjHCsDAyJzE3LjMiTiAxMDXCsDQ3JzIwLjkiRQ!5e0!3m2!1svi!2svn!4v1620000000000',
                'type' => 'textarea'
            ],
            [
                'group' => 'contact',
                'key' => 'footer_cta_title',
                'description' => 'Tiêu đề khối kêu gọi hành động ở chân trang (Footer CTA)',
                'recommendation' => 'Dòng tiêu đề thu hút ở cuối trang mời gọi khách hàng liên hệ đặt lịch.',
                'value' => 'Sẵn sàng tỏa sáng?',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'footer_cta_desc',
                'description' => 'Mô tả khối kêu gọi hành động ở chân trang',
                'recommendation' => 'Dòng mô tả ngắn gọn đi kèm số điện thoại hotline tư vấn.',
                'value' => 'Liên hệ ngay để nhận tư vấn phong cách và đặt lịch makeup.',
                'type' => 'text'
            ],
            [
                'group' => 'contact',
                'key' => 'footer_copyright',
                'description' => 'Bản quyền dưới chân trang (Footer Copyright)',
                'recommendation' => 'Nội dung thông tin bản quyền pháp lý hiển thị ở dưới cùng trang web.',
                'value' => "© " . date('Y') . " Thảo Makeup Artist. All Rights Reserved.",
                'type' => 'text'
            ],

            // --- NHÓM 3: MẠNG XÃ HỘI (social) ---
            [
                'group' => 'social',
                'key' => 'social_facebook',
                'description' => 'Đường dẫn trang Facebook / Fanpage',
                'recommendation' => 'Đường dẫn đầy đủ bắt đầu bằng https://facebook.com/... hoặc https://fb.com/...',
                'value' => 'https://facebook.com',
                'type' => 'text'
            ],
            [
                'group' => 'social',
                'key' => 'social_instagram',
                'description' => 'Đường dẫn trang Instagram',
                'recommendation' => 'Đường dẫn đầy đủ bắt đầu bằng https://instagram.com/...',
                'value' => 'https://instagram.com',
                'type' => 'text'
            ],
            [
                'group' => 'social',
                'key' => 'social_tiktok',
                'description' => 'Đường dẫn kênh TikTok',
                'recommendation' => 'Đường dẫn đầy đủ bắt đầu bằng https://tiktok.com/@...',
                'value' => 'https://tiktok.com',
                'type' => 'text'
            ],
            [
                'group' => 'social',
                'key' => 'social_zalo',
                'description' => 'Đường dẫn / Số Zalo tư vấn',
                'recommendation' => 'Link nhắn tin Zalo trực tiếp (Ví dụ: https://zalo.me/0912345678).',
                'value' => 'https://zalo.me',
                'type' => 'text'
            ],
            [
                'group' => 'social',
                'key' => 'social_youtube',
                'description' => 'Đường dẫn kênh YouTube',
                'recommendation' => 'Đường dẫn đầy đủ bắt đầu bằng https://youtube.com/@...',
                'value' => 'https://youtube.com',
                'type' => 'text'
            ],

            // --- NHÓM 4: TRANG CHỦ & TRƯỚC / SAU MAKEUP (home) ---
            [
                'group' => 'home',
                'key' => 'home_hero_subtitle',
                'description' => 'Phụ đề trên Banner đầu trang chủ',
                'recommendation' => 'Dòng chữ nhỏ phong cách cao cấp nằm phía trên tiêu đề chính (Ví dụ: High-End Experience).',
                'value' => 'High-End Experience',
                'type' => 'text'
            ],
            [
                'group' => 'home',
                'key' => 'home_intro_title',
                'description' => 'Tiêu đề khối giới thiệu triết lý trang chủ',
                'recommendation' => 'Tiêu đề nhấn mạnh phong cách nghệ thuật trang điểm (Ví dụ: Nghệ Thuật Của Sự Tinh Tế).',
                'value' => 'Nghệ Thuật Của Sự Tinh Tế',
                'type' => 'text'
            ],
            [
                'group' => 'home',
                'key' => 'home_slogan',
                'description' => 'Slogan / Châm ngôn sứ mệnh làm đẹp trang chủ',
                'recommendation' => 'Đoạn thông điệp ngắn gọn truyền tải cảm hứng làm đẹp và giá trị mang lại cho khách hàng.',
                'value' => 'Mỗi khuôn mặt là một tác phẩm nghệ thuật riêng biệt. Sứ mệnh của tôi là đánh thức vẻ đẹp rạng rỡ nhất ẩn sâu bên trong bạn.',
                'type' => 'textarea'
            ],
            [
                'group' => 'home',
                'key' => 'home_services_subtitle',
                'description' => 'Phụ đề khối dịch vụ trang chủ',
                'recommendation' => 'Dòng chữ nhỏ màu vàng gold phía trên tiêu đề dịch vụ (Ví dụ: Bảng giá & Dịch vụ).',
                'value' => 'Bảng giá & Dịch vụ',
                'type' => 'text'
            ],
            [
                'group' => 'home',
                'key' => 'home_services_title',
                'description' => 'Tiêu đề khối bảng giá dịch vụ trang chủ',
                'recommendation' => 'Tiêu đề chính mục dịch vụ (Ví dụ: Luxury Services).',
                'value' => 'Luxury Services',
                'type' => 'text'
            ],
            [
                'group' => 'home',
                'key' => 'home_transform_title',
                'description' => 'Tiêu đề khối biến hóa Trước / Sau (Before / After)',
                'recommendation' => 'Tiêu đề khối hiển thị 2 ảnh Trước / Sau song song tôn vinh sự biến hóa rạng rỡ (Ví dụ: The Magic Transformation).',
                'value' => 'The Magic Transformation',
                'type' => 'text'
            ],
            [
                'group' => 'home',
                'key' => 'home_transform_before',
                'description' => 'Ảnh trước khi trang điểm (Before) - Khung 9:16',
                'recommendation' => 'Khuyến nghị: Ảnh chân dung dọc chuẩn tỷ lệ 9:16 (kích thước đề xuất 1080x1920px hoặc 720x1280px). BẮT BUỘC cùng kích thước, tỷ lệ và góc chụp với ảnh After.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'home',
                'key' => 'home_transform_after',
                'description' => 'Ảnh sau khi trang điểm (After) - Khung 9:16',
                'recommendation' => 'Khuyến nghị: Ảnh chân dung dọc chuẩn tỷ lệ 9:16 (kích thước đề xuất 1080x1920px hoặc 720x1280px) cùng kích thước và góc chụp với ảnh Before.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'home',
                'key' => 'home_journal_title',
                'description' => 'Tiêu đề khối bài viết / Tạp chí trang chủ',
                'recommendation' => 'Tiêu đề mục bài viết chia sẻ kinh nghiệm mới nhất (Ví dụ: Journal / Tạp chí làm đẹp).',
                'value' => 'Journal',
                'type' => 'text'
            ],

            // --- NHÓM 5: TRANG GIỚI THIỆU & BIO ARTIST (about) ---
            [
                'group' => 'about',
                'key' => 'about_hero_bg',
                'description' => 'Ảnh nền banner đầu trang Giới thiệu',
                'recommendation' => 'Khuyến nghị: Ảnh ngang chất lượng cao tỷ lệ 16:9 hoặc 21:9, kích thước chuẩn 1920x800px, tone màu nude/sang trọng.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_hero_title',
                'description' => 'Tiêu đề chính banner trang Giới thiệu',
                'recommendation' => 'Tiêu đề trang trọng đầu trang (Ví dụ: Hành Trình Nghệ Thuật).',
                'value' => 'Hành Trình Nghệ Thuật',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'about_hero_desc',
                'description' => 'Mô tả ngắn banner trang Giới thiệu',
                'recommendation' => 'Dòng phụ đề ngắn gọn (Ví dụ: Tôn vinh vẻ đẹp nguyên bản).',
                'value' => 'Tôn vinh vẻ đẹp nguyên bản',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'about_me_title',
                'description' => 'Tiêu đề phần Câu chuyện Artist',
                'recommendation' => 'Tiêu đề chào mừng khách hàng (Hỗ trợ thẻ HTML như <br>, <span class="italic">...</span>).',
                'value' => 'Xin chào, tôi là <br><span class="italic">Makeup Artist</span>',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'about_me_content',
                'description' => 'Nội dung câu chuyện / Triết lý làm đẹp Artist',
                'recommendation' => 'Đoạn văn tự sự chia sẻ niềm đam mê, kinh nghiệm và phong cách makeup theo đuổi.',
                'value' => "Hành trình đến với nghệ thuật trang điểm của tôi bắt nguồn từ niềm đam mê mãnh liệt với cái đẹp và khát khao đánh thức sự tự tin trong mỗi người phụ nữ.\n\nHoạt động chuyên nghiệp, tôi theo đuổi phong cách Nude Luxury – tinh giản, sang trọng nhưng đầy chiều sâu.",
                'type' => 'textarea'
            ],
            [
                'group' => 'about',
                'key' => 'about_me_image',
                'description' => 'Ảnh chân dung nghệ sĩ trang điểm (Artist Portrait)',
                'recommendation' => 'Khuyến nghị: Ảnh chụp chân dung nghệ thuật tỷ lệ dọc 4:5, kích thước chuẩn 800x1000px, ánh sáng studio chuyên nghiệp.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_me_signature',
                'description' => 'Ảnh chữ ký nghệ sĩ (Signature)',
                'recommendation' => 'Khuyến nghị: Ảnh chữ ký định dạng PNG nền trong suốt (Transparent), màu tối hoặc ánh vàng gold, kích thước 400x200px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_video_cover',
                'description' => 'Ảnh bìa thước phim cảm hứng (Video Cover)',
                'recommendation' => 'Khuyến nghị: Ảnh ngang tỷ lệ 16:9, kích thước chuẩn 1280x720px hoặc 1920x1080px, hiển thị dưới nút Play video.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_video_link',
                'description' => 'Đường dẫn video YouTube cảm hứng',
                'recommendation' => 'Khuyến nghị: Đường dẫn video YouTube hợp lệ (Ví dụ: https://www.youtube.com/watch?v=maSuEtbPJ8Y).',
                'value' => 'https://www.youtube.com/watch?v=maSuEtbPJ8Y',
                'type' => 'video'
            ],
            [
                'group' => 'about',
                'key' => 'count_brides',
                'description' => 'Số lượng Cô dâu đã phục vụ (Thống kê)',
                'recommendation' => 'Nhập số nguyên để hệ thống chạy hiệu ứng số nhảy PureCounter (Ví dụ: 850).',
                'value' => '850',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'count_events',
                'description' => 'Số lượng Sự kiện & Nghệ sĩ đã makeup (Thống kê)',
                'recommendation' => 'Nhập số nguyên (Ví dụ: 320).',
                'value' => '320',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'count_years',
                'description' => 'Số năm kinh nghiệm làm nghề (Thống kê)',
                'recommendation' => 'Nhập số năm kinh nghiệm làm việc chuyên nghiệp (Ví dụ: 7).',
                'value' => '7',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'timeline_1_year',
                'description' => 'Mốc thời gian sự nghiệp 1 - Năm',
                'recommendation' => 'Năm bắt đầu cột mốc (Ví dụ: 2019).',
                'value' => '2019',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'timeline_1_text',
                'description' => 'Mốc thời gian sự nghiệp 1 - Nội dung',
                'recommendation' => 'Mô tả ngắn gọn sự kiện, khóa đào tạo hoặc giải thưởng đạt được.',
                'value' => 'Bắt đầu hành trình với các khóa đào tạo chuyên sâu tại Hà Nội.',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'timeline_2_year',
                'description' => 'Mốc thời gian sự nghiệp 2 - Năm',
                'recommendation' => 'Năm ghi nhận cột mốc tiếp theo (Ví dụ: 2021).',
                'value' => '2021',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'timeline_2_text',
                'description' => 'Mốc thời gian sự nghiệp 2 - Nội dung',
                'recommendation' => 'Mô tả sự kiện phát triển phong cách cá nhân và mở rộng thương hiệu.',
                'value' => 'Định hình phong cách Nude Luxury và phục vụ hàng trăm cô dâu.',
                'type' => 'text'
            ],
            [
                'group' => 'about',
                'key' => 'about_gallery_1',
                'description' => 'Ảnh dấu ấn làm đẹp 1 (Trang Giới thiệu)',
                'recommendation' => 'Khuyến nghị: Ảnh chụp dọc tỷ lệ 4:5 hoặc 3:4, kích thước chuẩn 600x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_gallery_2',
                'description' => 'Ảnh dấu ấn làm đẹp 2 (Trang Giới thiệu)',
                'recommendation' => 'Khuyến nghị: Ảnh chụp dọc tỷ lệ 4:5 hoặc 3:4, kích thước chuẩn 600x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_gallery_3',
                'description' => 'Ảnh dấu ấn làm đẹp 3 (Trang Giới thiệu)',
                'recommendation' => 'Khuyến nghị: Ảnh chụp dọc tỷ lệ 4:5 hoặc 3:4, kích thước chuẩn 600x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_academy_bg',
                'description' => 'Ảnh nền khối Đào tạo Academy',
                'recommendation' => 'Khuyến nghị: Ảnh ngang không gian lớp học/workshop, kích thước chuẩn 1920x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'about',
                'key' => 'about_academy_desc',
                'description' => 'Mô tả chương trình đào tạo học viên Academy',
                'recommendation' => 'Nội dung giới thiệu các khóa đào tạo từ makeup cá nhân đến chuyên nghiệp.',
                'value' => 'Khơi dậy tiềm năng nghệ thuật trong bạn. Các khóa đào tạo từ makeup cá nhân đến chuyên nghiệp, cam kết truyền lửa và đồng hành cùng đam mê.',
                'type' => 'textarea'
            ],

            // --- NHÓM 6: TRANG DỊCH VỤ & BÁO GIÁ (services) ---
            [
                'group' => 'services',
                'key' => 'service_hero_bg',
                'description' => 'Ảnh nền banner đầu trang Dịch vụ',
                'recommendation' => 'Khuyến nghị: Ảnh ngang chất lượng cao tỷ lệ 16:9 hoặc 21:9, kích thước chuẩn 1920x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'services',
                'key' => 'service_hero_title',
                'description' => 'Tiêu đề chính banner trang Dịch vụ',
                'recommendation' => 'Tiêu đề trang bảng giá dịch vụ (Ví dụ: Gói Dịch Vụ).',
                'value' => 'Gói Dịch Vụ',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'service_hero_subtitle',
                'description' => 'Phụ đề / Từ khóa nhấn mạnh trang Dịch vụ',
                'recommendation' => 'Từ khóa in nghiêng nhấn mạnh sự sang trọng (Ví dụ: Đặc Quyền).',
                'value' => 'Đặc Quyền',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'service_notes',
                'description' => 'Lưu ý quan trọng & Chính sách phụ thu dịch vụ',
                'recommendation' => 'Trình soạn thảo định dạng văn bản: Nhập các lưu ý về đặt cọc, phụ phí di chuyển ngoại tỉnh, thời gian làm việc sáng sớm...',
                'value' => '<ul><li>Báo giá trên đã bao gồm chi phí làm tóc cô dâu và phụ kiện cơ bản.</li><li>Khách hàng vui lòng đặt cọc tối thiểu 30% để giữ lịch chính thức.</li><li>Đối với dịch vụ ngoại thành và các tỉnh lân cận, vui lòng liên hệ trực tiếp để nhận báo giá phụ phí di chuyển.</li></ul>',
                'type' => 'rich_editor'
            ],
            [
                'group' => 'services',
                'key' => 'process_title',
                'description' => 'Tiêu đề khối quy trình làm việc',
                'recommendation' => 'Tiêu đề phần các bước phục vụ chuyên nghiệp (Ví dụ: Quy Trình Làm Việc).',
                'value' => 'Quy Trình Làm Việc',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_1_title',
                'description' => 'Quy trình Bước 1 - Tiêu đề',
                'recommendation' => 'Tên bước 1 trong quy trình (Ví dụ: Tư Vấn & Đặt Lịch).',
                'value' => 'Tư Vấn & Đặt Lịch',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_1_desc',
                'description' => 'Quy trình Bước 1 - Mô tả ngắn',
                'recommendation' => 'Mô tả chi tiết bước 1.',
                'value' => 'Lắng nghe mong muốn, phân tích tình trạng da và chốt concept phù hợp nhất.',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_2_title',
                'description' => 'Quy trình Bước 2 - Tiêu đề',
                'recommendation' => 'Tên bước 2 trong quy trình (Ví dụ: Giai Đoạn Chuẩn Bị).',
                'value' => 'Giai Đoạn Chuẩn Bị',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_2_desc',
                'description' => 'Quy trình Bước 2 - Mô tả ngắn',
                'recommendation' => 'Mô tả chi tiết bước 2.',
                'value' => 'Hướng dẫn bạn cách skincare trước ngày quan trọng để lớp nền ăn tệp hoàn hảo.',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_3_title',
                'description' => 'Quy trình Bước 3 - Tiêu đề',
                'recommendation' => 'Tên bước 3 trong quy trình (Ví dụ: Ngày Tỏa Sáng).',
                'value' => 'Ngày Tỏa Sáng',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_3_desc',
                'description' => 'Quy trình Bước 3 - Mô tả ngắn',
                'recommendation' => 'Mô tả chi tiết bước 3.',
                'value' => 'Đến đúng giờ, chuẩn bị mỹ phẩm high-end chính hãng, bộ cọ vệ sinh sạch khuẩn và thực hiện makeup chuyên nghiệp tôn vinh vẻ đẹp riêng.',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_4_title',
                'description' => 'Quy trình Bước 4 - Tiêu đề',
                'recommendation' => 'Tên bước 4 trong quy trình (Ví dụ: Hoàn Thiện).',
                'value' => 'Hoàn Thiện',
                'type' => 'text'
            ],
            [
                'group' => 'services',
                'key' => 'process_step_4_desc',
                'description' => 'Quy trình Bước 4 - Mô tả ngắn',
                'recommendation' => 'Mô tả chi tiết bước 4.',
                'value' => 'Dặm lại lần cuối, mặc trang phục và lưu lại những khung hình rạng rỡ nhất.',
                'type' => 'text'
            ],

            // --- NHÓM 7: TRANG BỘ SƯU TẬP (portfolio) ---
            [
                'group' => 'portfolio',
                'key' => 'portfolio_hero_bg',
                'description' => 'Ảnh nền banner đầu trang Bộ sưu tập',
                'recommendation' => 'Khuyến nghị: Ảnh ngang chất lượng cao, kích thước chuẩn 1920x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'portfolio',
                'key' => 'portfolio_title',
                'description' => 'Tiêu đề chính banner Portfolio',
                'recommendation' => 'Tiêu đề trang bộ sưu tập (Ví dụ: Dấu Ấn).',
                'value' => 'Dấu Ấn',
                'type' => 'text'
            ],
            [
                'group' => 'portfolio',
                'key' => 'portfolio_subtitle',
                'description' => 'Phụ đề banner Portfolio',
                'recommendation' => 'Từ khóa phong cách (Ví dụ: Nghệ Thuật).',
                'value' => 'Nghệ Thuật',
                'type' => 'text'
            ],
            [
                'group' => 'portfolio',
                'key' => 'portfolio_desc',
                'description' => 'Mô tả ngắn trang Bộ sưu tập',
                'recommendation' => 'Đoạn giới thiệu tổng quan về các tác phẩm cô dâu, sự kiện và nghệ thuật.',
                'value' => 'Khám phá những khoảnh khắc rạng rỡ nhất qua góc nhìn Nude Luxury.',
                'type' => 'textarea'
            ],

            // --- NHÓM 8: TRANG TẠP CHÍ & TÁC GIẢ (blog) ---
            [
                'group' => 'blog',
                'key' => 'blog_hero_bg',
                'description' => 'Ảnh nền banner đầu trang Tạp chí',
                'recommendation' => 'Khuyến nghị: Ảnh ngang chất lượng cao, kích thước chuẩn 1920x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'blog',
                'key' => 'blog_hero_pretitle',
                'description' => 'Tiêu đề phụ trên cùng trang Tạp chí',
                'recommendation' => 'Dòng chữ nhỏ phong cách editorial (Ví dụ: The Beauty Journal).',
                'value' => 'The Beauty Journal',
                'type' => 'text'
            ],
            [
                'group' => 'blog',
                'key' => 'blog_hero_title',
                'description' => 'Tiêu đề chính banner trang Tạp chí',
                'recommendation' => 'Tiêu đề chính trang bài viết (Ví dụ: Góc Chia Sẻ).',
                'value' => 'Góc Chia Sẻ',
                'type' => 'text'
            ],
            [
                'group' => 'blog',
                'key' => 'blog_hero_subtitle',
                'description' => 'Phụ đề banner trang Tạp chí',
                'recommendation' => 'Từ khóa nhấn mạnh (Ví dụ: Kiến Thức).',
                'value' => 'Kiến Thức',
                'type' => 'text'
            ],
            [
                'group' => 'blog',
                'key' => 'blog_hero_desc',
                'description' => 'Mô tả ngắn trang Tạp chí',
                'recommendation' => 'Giới thiệu mục đích các bài viết chia sẻ kinh nghiệm trang điểm và bí quyết chăm sóc da.',
                'value' => 'Nơi cập nhật những xu hướng trang điểm mới nhất và bí quyết chăm sóc sắc đẹp từ chuyên gia.',
                'type' => 'textarea'
            ],
            [
                'group' => 'blog',
                'key' => 'author_name',
                'description' => 'Tên tác giả bài viết / Founder',
                'recommendation' => 'Tên nghệ sĩ hiển thị ở khối tác giả chân bài viết (Ví dụ: Thảo).',
                'value' => 'Thảo Makeup',
                'type' => 'text'
            ],
            [
                'group' => 'blog',
                'key' => 'author_title',
                'description' => 'Chức danh tác giả',
                'recommendation' => 'Chức danh nghề nghiệp (Ví dụ: Makeup Artist / Founder).',
                'value' => 'Makeup Artist / Founder',
                'type' => 'text'
            ],
            [
                'group' => 'blog',
                'key' => 'author_avatar',
                'description' => 'Ảnh đại diện tác giả (Avatar)',
                'recommendation' => 'Khuyến nghị: Ảnh hình vuông tỷ lệ 1:1, kích thước chuẩn 500x500px, chân dung rõ nét.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'blog',
                'key' => 'author_bio',
                'description' => 'Tiểu sử ngắn tác giả chân bài viết',
                'recommendation' => 'Đoạn giới thiệu 2-3 câu ngắn gọn về đam mê và phong cách của nghệ sĩ.',
                'value' => 'Đam mê tôn vinh vẻ đẹp nguyên bản thông qua phong cách trang điểm Nude Luxury chuyên nghiệp.',
                'type' => 'textarea'
            ],

            // --- NHÓM 9: TRANG ĐẶT LỊCH & LỜI NHẮN (booking) ---
            [
                'group' => 'booking',
                'key' => 'booking_pretitle',
                'description' => 'Dòng giới thiệu nhỏ trang Đặt lịch',
                'recommendation' => 'Dòng chữ nhỏ phía trên tiêu đề form đặt lịch (Ví dụ: Connect with us).',
                'value' => 'Connect with us',
                'type' => 'text'
            ],
            [
                'group' => 'booking',
                'key' => 'booking_title',
                'description' => 'Tiêu đề chính trang Đặt lịch',
                'recommendation' => 'Tiêu đề form đặt lịch (Hỗ trợ định dạng HTML như <br>, <span class="italic font-light">...).',
                'value' => 'Gửi Lời Nhắn <br> <span class="italic font-light">Tư Vấn & Đặt Lịch</span>',
                'type' => 'text'
            ],
            [
                'group' => 'booking',
                'key' => 'booking_coverage_note',
                'description' => 'Ghi chú phạm vi nhận lịch hẹn',
                'recommendation' => 'Ghi chú về dịch vụ phục vụ tận nơi cô dâu và sự kiện.',
                'value' => '* Nhận booking phục vụ tận nơi cho cô dâu & sự kiện toàn quốc.',
                'type' => 'text'
            ],
            [
                'group' => 'booking',
                'key' => 'booking_placeholder_social',
                'description' => 'Gợi ý nhập link mạng xã hội của khách',
                'recommendation' => 'Văn bản gợi ý hiển thị bên trong ô input link mạng xã hội của khách.',
                'value' => 'Link trang cá nhân để xem phong cách của bạn',
                'type' => 'text'
            ],
            [
                'group' => 'booking',
                'key' => 'booking_date_note',
                'description' => 'Ghi chú dưới mục chọn ngày hẹn',
                'recommendation' => 'Hướng dẫn khách hàng chọn ngày bắt đầu khi sự kiện diễn ra nhiều ngày.',
                'value' => 'Nếu sự kiện diễn ra trong nhiều ngày (VD: Ăn hỏi + Cưới), vui lòng chọn ngày khởi đầu. Chúng tôi sẽ chốt chi tiết lịch trình sau khi tư vấn.',
                'type' => 'textarea'
            ],
            [
                'group' => 'booking',
                'key' => 'booking_placeholder_message',
                'description' => 'Gợi ý lời nhắn yêu cầu đặc biệt của khách',
                'recommendation' => 'Văn bản placeholder gợi ý nội dung trong khung lời nhắn.',
                'value' => 'Hãy chia sẻ về địa điểm, phong cách mong muốn hoặc yêu cầu cụ thể của bạn...',
                'type' => 'textarea'
            ],
            [
                'group' => 'booking',
                'key' => 'booking_success_message',
                'description' => 'Thông báo sau khi khách gửi form thành công',
                'recommendation' => 'Lời cảm ơn và xác nhận hiển thị ngay sau khi khách hàng bấm Gửi yêu cầu đặt lịch.',
                'value' => 'Cảm ơn quý khách! Chúng tôi đã nhận được thông tin và sẽ liên hệ lại với bạn sớm nhất!',
                'type' => 'textarea'
            ],

            // --- NHÓM 10: THANH TOÁN & HÓA ĐƠN (banking) ---
            [
                'group' => 'banking',
                'key' => 'bank_name',
                'description' => 'Tên ngân hàng nhận chuyển khoản',
                'recommendation' => 'Tên ngân hàng chính thức nhận tiền chuyển khoản (Ví dụ: MB Bank, Vietcombank, Techcombank).',
                'value' => 'MB Bank (Ngân hàng Quân Đội)',
                'type' => 'text'
            ],
            [
                'group' => 'banking',
                'key' => 'bank_account_number',
                'description' => 'Số tài khoản ngân hàng nhận tiền',
                'recommendation' => 'Dãy số tài khoản chính xác để khách hàng quét/chuyển khoản.',
                'value' => '0866072800',
                'type' => 'text'
            ],
            [
                'group' => 'banking',
                'key' => 'bank_account_holder',
                'description' => 'Tên chủ tài khoản (Viết hoa không dấu)',
                'recommendation' => 'Họ và tên chủ tài khoản viết hoa không dấu (Ví dụ: NGUYEN PHUONG THAO).',
                'value' => 'NGUYEN PHUONG THAO',
                'type' => 'text'
            ],
            [
                'group' => 'banking',
                'key' => 'bank_qr_image',
                'description' => 'Ảnh mã QR chuyển khoản VietQR',
                'recommendation' => 'Khuyến nghị: Tải lên ảnh mã QR chất lượng cao, hình vuông kích thước 600x600px hoặc 800x800px, nền trắng quét rõ nét trên mọi app ngân hàng.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'banking',
                'key' => 'invoice_footer_note',
                'description' => 'Lời cảm ơn & Ghi chú cuối hóa đơn dịch vụ',
                'recommendation' => 'Lời cảm ơn khách hàng in ở chân hóa đơn thanh toán / PDF hóa đơn.',
                'value' => 'Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của chúng tôi!',
                'type' => 'textarea'
            ],

            // --- NHÓM 11: POPUP THÔNG BÁO & KHUYẾN MÃI (popup) ---
            [
                'group' => 'popup',
                'key' => 'popup_active',
                'description' => 'Bật / Tắt popup khuyến mãi trang chủ',
                'recommendation' => 'Chọn 1 để bật popup hiển thị tự động sau 1 giây khi khách vào trang chủ, chọn 0 để tắt.',
                'value' => '0',
                'type' => 'toggle'
            ],
            [
                'group' => 'popup',
                'key' => 'popup_title',
                'description' => 'Tiêu đề popup khuyến mãi',
                'recommendation' => 'Tiêu đề chương trình ưu đãi (Ví dụ: Ưu Đãi Đặc Biệt Mùa Cưới 2026).',
                'value' => 'Ưu Đãi Đặc Biệt Mùa Cưới',
                'type' => 'text'
            ],
            [
                'group' => 'popup',
                'key' => 'popup_desc',
                'description' => 'Mô tả nội dung trên popup',
                'recommendation' => 'Chi tiết quyền lợi ưu đãi và thời hạn áp dụng.',
                'value' => 'Giảm ngay 10% cho các gói makeup cô dâu khi đặt lịch trước 30 ngày.',
                'type' => 'textarea'
            ],
            [
                'group' => 'popup',
                'key' => 'popup_image',
                'description' => 'Ảnh banner hiển thị trên popup',
                'recommendation' => 'Khuyến nghị: Ảnh ngang hoặc vuông tỷ lệ 4:3 hoặc 16:9, kích thước chuẩn 800x600px hoặc 1000x800px.',
                'value' => null,
                'type' => 'image'
            ],
            [
                'group' => 'popup',
                'key' => 'popup_link',
                'description' => 'Đường dẫn khi click vào nút Xem chi tiết trên popup',
                'recommendation' => 'Đường dẫn chuyển hướng (Ví dụ: /booking hoặc https://zalo.me/...).',
                'value' => '/booking',
                'type' => 'text'
            ],

            // --- NHÓM 12: GIAO DIỆN, MÀU SẮC & PHÔNG CHỮ (theme) ---
            [
                'group' => 'theme',
                'key' => 'theme_preset',
                'description' => 'Chủ đề giao diện mẫu đã áp dụng (Preset Theme)',
                'recommendation' => 'Lựa chọn các bộ giao diện phối màu và phông chữ chuẩn ngành (nude_luxury, rose_gold, minimalist, emerald, royal_velvet).',
                'value' => 'nude_luxury',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_color_primary',
                'description' => 'Màu nền chính website (Primary Background Color)',
                'recommendation' => 'Mã màu Hex nền toàn trang (Ví dụ: #f6f1ec cho Nude Luxury, #faf0f2 cho Rose Gold, #f8f8f8 cho Minimalist).',
                'value' => '#f6f1ec',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_color_gold',
                'description' => 'Màu điểm nhấn thương hiệu (Accent / Gold Color)',
                'recommendation' => 'Mã màu Hex cho các chi tiết điểm nhấn, nhãn tag, icon, đường viền vàng đồng (Ví dụ: #c8a98d).',
                'value' => '#c8a98d',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_color_dark',
                'description' => 'Màu tối chủ đạo / Tiêu đề (Dark / Contrast Color)',
                'recommendation' => 'Mã màu Hex cho tiêu đề chính, khối footer, màu chữ đậm và độ tương phản cao (Ví dụ: #3e2f2f).',
                'value' => '#3e2f2f',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_color_button',
                'description' => 'Màu nền nút bấm chính (Button Primary Background)',
                'recommendation' => 'Mã màu Hex nền các nút CTA như Gửi yêu cầu đặt lịch, Đóng modal, Nút chuyển hướng (Ví dụ: #3e2f2f).',
                'value' => '#3e2f2f',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_color_button_text',
                'description' => 'Màu chữ nút bấm chính (Button Primary Text Color)',
                'recommendation' => 'Mã màu Hex chữ của nút bấm (Mặc định: #ffffff).',
                'value' => '#ffffff',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_font_heading_type',
                'description' => 'Nguồn phông chữ Tiêu đề (google hoặc custom)',
                'recommendation' => 'Chọn "google" để sử dụng kho Google Fonts hoặc "custom" để sử dụng file font tải lên từ máy tính.',
                'value' => 'google',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_font_heading',
                'description' => 'Tên phông chữ Tiêu đề Google Fonts (Heading Font)',
                'recommendation' => 'Tên phông chữ Google Font cho các thẻ tiêu đề (H1, H2, H3, H4) như Playfair Display, Cormorant Garamond, Cinzel, Lora, Merriweather, Montserrat, Prata, Bodoni Moda.',
                'value' => 'Playfair Display',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_custom_heading_font_file',
                'description' => 'File phông chữ Tiêu đề tùy chỉnh tải lên (.woff2, .woff, .ttf, .otf)',
                'recommendation' => 'Khuyến nghị: File font việt hóa chất lượng cao định dạng .woff2 hoặc .ttf để tốc độ tải nhanh nhất.',
                'value' => null,
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_font_body_type',
                'description' => 'Nguồn phông chữ Nội dung (google hoặc custom)',
                'recommendation' => 'Chọn "google" hoặc "custom".',
                'value' => 'google',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_font_body',
                'description' => 'Tên phông chữ Nội dung Google Fonts (Body Font)',
                'recommendation' => 'Tên phông chữ Google Font cho văn bản, mô tả, nút bấm như Inter, Plus Jakarta Sans, Be Vietnam Pro, Montserrat, Roboto, Open Sans, Nunito, Quicksand.',
                'value' => 'Inter',
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_custom_body_font_file',
                'description' => 'File phông chữ Nội dung tùy chỉnh tải lên (.woff2, .woff, .ttf, .otf)',
                'recommendation' => 'File font định dạng .woff2, .woff, .ttf hoặc .otf.',
                'value' => null,
                'type' => 'text'
            ],
            [
                'group' => 'theme',
                'key' => 'theme_custom_css',
                'description' => 'Mã CSS tùy chỉnh bổ sung (Custom CSS Injection)',
                'recommendation' => 'Mã CSS tùy biến viết thêm cho trang web (Dành cho nhà thiết kế/kỹ thuật nâng cao).',
                'value' => null,
                'type' => 'textarea'
            ],

            // --- NHÓM 13: MÁY CHỦ EMAIL & SMTP (mail) ---
            [
                'group' => 'mail',
                'key' => 'mail_mailer',
                'description' => 'Phương thức gửi Mail (Mailer)',
                'recommendation' => 'Mặc định: smtp (hoặc sendmail, log).',
                'value' => 'smtp',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_host',
                'description' => 'Máy chủ gửi Mail SMTP Host',
                'recommendation' => 'Địa chỉ máy chủ SMTP (Ví dụ: smtp.gmail.com hoặc mail.domain.com).',
                'value' => 'smtp.gmail.com',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_port',
                'description' => 'Cổng kết nối SMTP Port',
                'recommendation' => 'Cổng kết nối máy chủ (Thường là 587 với TLS hoặc 465 với SSL).',
                'value' => '587',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_encryption',
                'description' => 'Giao thức mã hóa SMTP Encryption',
                'recommendation' => 'Giao thức bảo mật (tls, ssl hoặc none).',
                'value' => 'tls',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_username',
                'description' => 'Tài khoản Email đăng nhập SMTP',
                'recommendation' => 'Địa chỉ email dùng để xác thực gửi thư.',
                'value' => '',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_password',
                'description' => 'Mật khẩu Email / Mật khẩu ứng dụng SMTP',
                'recommendation' => 'Mật khẩu hòm thư hoặc App Password 16 ký tự Google.',
                'value' => '',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_from_address',
                'description' => 'Email gửi đi hiển thị (From Address)',
                'recommendation' => 'Email hiển thị khi khách nhận được thư (Ví dụ: booking@domain.com).',
                'value' => 'no-reply@example.com',
                'type' => 'text'
            ],
            [
                'group' => 'mail',
                'key' => 'mail_from_name',
                'description' => 'Tên người gửi hiển thị (From Name)',
                'recommendation' => 'Tên thương hiệu xuất hiện trong email (Ví dụ: Linh Makeup Studio).',
                'value' => 'Studio Website',
                'type' => 'text'
            ],

            // --- NHÓM 14: THÔNG BÁO TELEGRAM (telegram) ---
            [
                'group' => 'telegram',
                'key' => 'telegram_notify_enabled',
                'description' => 'Bật / Tắt thông báo đặt lịch qua Telegram Bot',
                'recommendation' => 'Chọn 1 để bật nhận tin nhắn báo chuông về điện thoại khi có khách đặt lịch mới, chọn 0 để tắt.',
                'value' => '0',
                'type' => 'toggle'
            ],
            [
                'group' => 'telegram',
                'key' => 'telegram_bot_token',
                'description' => 'Mã Token của Telegram Bot riêng',
                'recommendation' => 'Mã Token do @BotFather cấp khi tạo Bot riêng trên Telegram.',
                'value' => '',
                'type' => 'text'
            ],
            [
                'group' => 'telegram',
                'key' => 'telegram_chat_id',
                'description' => 'Chat ID Telegram nhận thông báo',
                'recommendation' => 'ID tài khoản cá nhân hoặc ID của Group chat nhận tin (lấy từ @userinfobot).',
                'value' => '',
                'type' => 'text'
            ],

            // --- NHÓM 15: THÔNG BÁO ZALO ZNS (zalo) ---
            [
                'group' => 'zalo',
                'key' => 'zalo_notify_enabled',
                'description' => 'Bật / Tắt thông báo Zalo ZNS',
                'recommendation' => 'Chọn 1 để bật tích hợp Zalo ZNS doanh nghiệp, chọn 0 để tắt.',
                'value' => '0',
                'type' => 'toggle'
            ],
            [
                'group' => 'zalo',
                'key' => 'zalo_oa_id',
                'description' => 'Zalo Official Account ID (OA ID)',
                'recommendation' => 'Mã ID Zalo OA doanh nghiệp.',
                'value' => '',
                'type' => 'text'
            ],
            [
                'group' => 'zalo',
                'key' => 'zalo_app_id',
                'description' => 'Zalo App ID',
                'recommendation' => 'Mã App ID trên Zalo for Developers.',
                'value' => '',
                'type' => 'text'
            ],
            [
                'group' => 'zalo',
                'key' => 'zalo_secret_key',
                'description' => 'Zalo App Secret Key',
                'recommendation' => 'Mã khóa bí mật Secret Key của Zalo App.',
                'value' => '',
                'type' => 'text'
            ],
            [
                'group' => 'zalo',
                'key' => 'zalo_template_id',
                'description' => 'Mã mẫu tin nhắn ZNS Đặt lịch (Template ID)',
                'recommendation' => 'Mã Template ID được duyệt bởi Zalo.',
                'value' => '',
                'type' => 'text'
            ],
            [
                'group' => 'zalo',
                'key' => 'zalo_access_token',
                'description' => 'Zalo Access Token',
                'recommendation' => 'Access Token hoặc Refresh Token của Zalo OA.',
                'value' => '',
                'type' => 'text'
            ],
        ];

        foreach ($standardSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'description' => $setting['description'],
                    'recommendation' => $setting['recommendation'] ?? null,
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }

        // 3. Bảng Banners
        Banner::insertOrIgnore([
            ['title' => 'BST Cô Dâu Mùa Xuân 2026', 'image_path' => 'banner1.jpg', 'position' => 'home_hero', 'is_active' => true],
            ['title' => 'Ưu đãi Makeup Tiệc Nhóm', 'image_path' => 'banner2.jpg', 'position' => 'home_hero', 'is_active' => true],
            ['title' => 'Khóa học Cá nhân Cấp tốc', 'image_path' => 'banner3.jpg', 'position' => 'popup', 'is_active' => true],
        ]);

        // 4. Bảng Categories
        $catPost = Category::firstOrCreate(['slug' => 'meo-lam-dep'], ['name' => 'Mẹo làm đẹp', 'type' => Category::TYPE_POST]);
        $catTrend = Category::firstOrCreate(['slug' => 'xu-huong-bridal'], ['name' => 'Xu hướng Bridal', 'type' => Category::TYPE_POST]);
        $catService = Category::firstOrCreate(['slug' => 'dich-vu-cuoi'], ['name' => 'Dịch vụ Cưới', 'type' => Category::TYPE_SERVICE]);
        $catPortfolio = Category::firstOrCreate(['slug' => 'co-dau'], ['name' => 'Cô dâu', 'type' => Category::TYPE_PORTFOLIO]);

        // 5. Bảng Services
        $s1 = Service::firstOrCreate(
            ['name' => 'Makeup Cô Dâu (Lễ ăn hỏi)'],
            [
                'category_id' => $catService->id,
                'price_text' => '1.500.000 VNĐ',
                'service_level' => Service::LEVEL_BASIC,
                'features' => ['Làm tóc cơ bản', 'Mỹ phẩm cao cấp', 'Dặm phấn tại nhà'],
                'is_active' => true,
                'is_featured' => true,
            ]
        );
        $s2 = Service::firstOrCreate(
            ['name' => 'Makeup Cô Dâu (Tiệc cưới chính)'],
            [
                'category_id' => $catService->id,
                'price_text' => '2.500.000 VNĐ',
                'service_level' => Service::LEVEL_PREMIUM,
                'features' => ['Làm tóc cầu kỳ', 'Dán mi cao cấp', 'Phụ kiện cài tóc', 'Đi theo dặm phấn'],
                'is_active' => true,
                'is_featured' => true,
            ]
        );
        $s3 = Service::firstOrCreate(
            ['name' => 'Khóa học Makeup cá nhân'],
            [
                'category_id' => $catService->id,
                'price_text' => '2.000.000 VNĐ',
                'service_level' => Service::LEVEL_EXTRA,
                'features' => ['3 buổi học', 'Hỗ trợ cốp đồ', 'Tặng bộ cọ'],
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        // 6. Bảng Posts
        for ($i = 1; $i <= 3; $i++) {
            Post::firstOrCreate(
                ['slug' => "bi-quyet-giu-nen-$i"],
                [
                    'category_id' => $catPost->id,
                    'title' => "Bí quyết giữ lớp nền lâu trôi số $i",
                    'excerpt' => "Hướng dẫn chi tiết cách đánh nền cho da dầu...",
                    'content' => "<p>Nội dung bài viết chia sẻ kinh nghiệm makeup thực tế từ chuyên gia...</p>",
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );
        }

        // 7. Bảng Portfolios
        Portfolio::firstOrCreate(
            ['title' => 'Cô dâu rạng rỡ'],
            ['category_id' => $catPortfolio->id, 'type' => 'image', 'file_path' => 'p1.jpg', 'is_active' => true]
        );
        Portfolio::firstOrCreate(
            ['title' => 'Makeup kỷ yếu'],
            ['category_id' => $catPortfolio->id, 'type' => 'image', 'file_path' => 'p2.jpg', 'is_active' => true]
        );
        Portfolio::firstOrCreate(
            ['title' => 'Đi tiệc cuối năm'],
            ['category_id' => $catPortfolio->id, 'type' => 'image', 'file_path' => 'p3.jpg', 'is_active' => true]
        );

        // 8. Bảng Bookings
        $names = ['Phương Ly', 'Thu Hà', 'Mai Anh', 'Bích Ngọc', 'Tuyết Nhung'];
        $statuses = ['pending', 'confirmed', 'completed', 'canceled'];

        foreach ($names as $key => $name) {
            Booking::firstOrCreate(
                ['phone' => '09' . str_pad((string)(10000000 + $key), 8, '0', STR_PAD_LEFT)],
                [
                    'customer_name' => "Nguyễn $name",
                    'service_ids' => [($key % 2 == 0) ? $s1->id : $s2->id],
                    'booking_date' => now()->addDays(rand(1, 20))->addHours(rand(8, 18)),
                    'message' => "Khách hàng book lịch vào ngày " . now()->addDays(rand(1, 5))->format('d/m'),
                    'status' => $statuses[array_rand($statuses)],
                    'total_amount' => ($key % 2 == 0) ? 1500000 : 500000,
                ]
            );
        }
    }
}