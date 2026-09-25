<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Payment;
use App\Models\Expense;
use Carbon\Carbon;

class SampleFinancialSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt Observers để không gửi email/telegram notification khi seed dữ liệu mẫu
        Booking::unsetEventDispatcher();
        Payment::unsetEventDispatcher();

        // Xóa dữ liệu cũ của bảng thu chi và booking mẫu nếu có để đảm bảo tính nhất quán
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Payment::truncate();
        Expense::truncate();
        BookingItem::truncate();
        Booking::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // -----------------------------------------------------------------------------
        // DỮ LIỆU CÁC THÁNG TỪ THÁNG 1/2026 ĐẾN THÁNG 9/2026
        // -----------------------------------------------------------------------------

        $monthlyData = [
            // THÁNG 1/2026
            1 => [
                'bookings' => [
                    [
                        'name' => 'Lê Thị Hồng Nhung',
                        'phone' => '0903123456',
                        'date' => '2026-01-12 08:30:00',
                        'status' => 'completed',
                        'total' => 4500000,
                        'items' => [
                            ['name' => 'Trang Điểm Cô Dâu VIP (Lễ gia tiên & Tiệc)', 'price' => 4500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Đặt cọc giữ lịch', 'amount' => 1500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-01-05 10:15:00'],
                            ['title' => 'Thanh toán nốt sau tiệc', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-01-12 12:45:00'],
                        ],
                    ],
                    [
                        'name' => 'Nguyễn Bích Trâm',
                        'phone' => '0912456789',
                        'date' => '2026-01-18 07:00:00',
                        'status' => 'completed',
                        'total' => 6000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ngày Cưới Cao Cấp', 'price' => 4000000, 'qty' => 1],
                            ['name' => 'Makeup Mẹ Cô Dâu & Mẹ Chú Rể', 'price' => 2000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Đặt cọc giữ lịch 40%', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-01-08 14:20:00'],
                            ['title' => 'Thanh toán hoàn tất', 'amount' => 4000000, 'method' => 'Tiền mặt', 'date' => '2026-01-18 11:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Trần Thu Hà',
                        'phone' => '0988776655',
                        'date' => '2026-01-22 15:00:00',
                        'status' => 'completed',
                        'total' => 3600000,
                        'items' => [
                            ['name' => 'Makeup Tiệc Year End Party (3 khách)', 'price' => 3600000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán trọn gói tiệc tất niên', 'amount' => 3600000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-01-22 17:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Phạm Quỳnh Anh',
                        'phone' => '0977112233',
                        'date' => '2026-01-25 09:00:00',
                        'status' => 'completed',
                        'total' => 5500000,
                        'items' => [
                            ['name' => 'Makeup Lookbook BST Áo Dài Tết', 'price' => 5500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán hợp đồng Lookbook Tết', 'amount' => 5500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-01-25 18:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Đỗ Mai Hương',
                        'phone' => '0934567890',
                        'date' => '2026-01-28 08:00:00',
                        'status' => 'completed',
                        'total' => 2500000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ăn Hỏi Tone Nude', 'price' => 2500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc giữ lịch', 'amount' => 1000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-01-10 09:00:00'],
                            ['title' => 'Thanh toán nốt sau lễ', 'amount' => 1500000, 'method' => 'Tiền mặt', 'date' => '2026-01-28 11:00:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T1/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-01-02'],
                    ['name' => 'Kem nền Dior Forever & Phấn Chanel', 'category' => 'Mỹ phẩm', 'amount' => 3200000, 'date' => '2026-01-06'],
                    ['name' => 'Tiền điện, nước & Wifi Studio', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1100000, 'date' => '2026-01-10'],
                    ['name' => 'Phụ kiện cài tóc cô dâu & hoa tươi cài áo', 'category' => 'Phụ kiện', 'amount' => 1500000, 'date' => '2026-01-14'],
                    ['name' => 'Chạy quảng cáo Facebook Ads Tết', 'category' => 'Marketing', 'amount' => 1000000, 'date' => '2026-01-15'],
                ],
            ],

            // THÁNG 2/2026
            2 => [
                'bookings' => [
                    [
                        'name' => 'Vũ Minh Anh',
                        'phone' => '0966554433',
                        'date' => '2026-02-15 07:30:00',
                        'status' => 'completed',
                        'total' => 5000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Lễ Cưới Đầu Xuân', 'price' => 5000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc giữ ngày', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-02-04 11:00:00'],
                            ['title' => 'Thanh toán sau tiệc', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-02-15 13:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Đặng Thùy Dương',
                        'phone' => '0918273645',
                        'date' => '2026-02-10 09:00:00',
                        'status' => 'completed',
                        'total' => 2800000,
                        'items' => [
                            ['name' => 'Makeup Chụp Ảnh Du Xuân Ngoại Cảnh', 'price' => 2800000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán dịch vụ chụp xuân', 'amount' => 2800000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-02-10 14:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Ngô Bảo Châu',
                        'phone' => '0979887766',
                        'date' => '2026-02-22 08:00:00',
                        'status' => 'completed',
                        'total' => 4200000,
                        'items' => [
                            ['name' => 'Combo Makeup Cô Dâu & Mẹ Chú Rể', 'price' => 4200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc 30%', 'amount' => 1500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-02-12 15:30:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 2700000, 'method' => 'Tiền mặt', 'date' => '2026-02-22 11:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Trịnh Kim Ngân',
                        'phone' => '0938123987',
                        'date' => '2026-02-24 16:00:00',
                        'status' => 'completed',
                        'total' => 2400000,
                        'items' => [
                            ['name' => 'Makeup Dự Tiệc Tân Niên Doanh Nghiệp', 'price' => 2400000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán tiệc tân niên', 'amount' => 2400000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-02-24 18:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Phan Lan Anh',
                        'phone' => '0908765432',
                        'date' => '2026-02-26 09:30:00',
                        'status' => 'completed',
                        'total' => 2300000,
                        'items' => [
                            ['name' => 'Makeup Kỷ Niệm Ngày Cưới', 'price' => 2300000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán trực tiếp', 'amount' => 2300000, 'method' => 'Tiền mặt', 'date' => '2026-02-26 12:00:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T2/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-02-02'],
                    ['name' => 'Tiền điện, nước & Wifi T2', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 950000, 'date' => '2026-02-10'],
                    ['name' => 'Set son lì cao cấp Tom Ford & YSL', 'category' => 'Mỹ phẩm', 'amount' => 2100000, 'date' => '2026-02-18'],
                    ['name' => 'Trà, cà phê & bánh tiếp khách mùa Tết', 'category' => 'Vận hành', 'amount' => 450000, 'date' => '2026-02-20'],
                ],
            ],

            // THÁNG 3/2026 (Ngày 8/3 & Mùa Cưới)
            3 => [
                'bookings' => [
                    [
                        'name' => 'Nguyễn Thị Hoài Thương',
                        'phone' => '0983344556',
                        'date' => '2026-03-10 07:00:00',
                        'status' => 'completed',
                        'total' => 6500000,
                        'items' => [
                            ['name' => 'Gói Makeup Cô Dâu VIP Trọn Gói', 'price' => 6500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc gói VIP', 'amount' => 2500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-01 09:00:00'],
                            ['title' => 'Thanh toán hoàn tất', 'amount' => 4000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-10 12:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Hoàng Thảo My',
                        'phone' => '0919988776',
                        'date' => '2026-03-08 14:00:00',
                        'status' => 'completed',
                        'total' => 5500000,
                        'items' => [
                            ['name' => 'Makeup Sự Kiện Gala 8/3 (5 khách)', 'price' => 5500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán sự kiện 8/3', 'amount' => 5500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-08 17:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Bùi Tuyết Mai',
                        'phone' => '0909123890',
                        'date' => '2026-03-16 08:00:00',
                        'status' => 'completed',
                        'total' => 4800000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ăn Hỏi & Đón Dâu', 'price' => 4800000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc giữ lịch', 'amount' => 1800000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-05 11:00:00'],
                            ['title' => 'Thanh toán sau tiệc', 'amount' => 3000000, 'method' => 'Tiền mặt', 'date' => '2026-03-16 13:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Đinh Ngọc Diệp',
                        'phone' => '0967889900',
                        'date' => '2026-03-18 10:00:00',
                        'status' => 'completed',
                        'total' => 3200000,
                        'items' => [
                            ['name' => 'Makeup Profile Doanh Nhân Cao Cấp', 'price' => 3200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán chụp profile', 'amount' => 3200000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-18 13:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Lê Cẩm Tú',
                        'phone' => '0937665544',
                        'date' => '2026-03-25 08:30:00',
                        'status' => 'completed',
                        'total' => 5000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Outdoor Wedding', 'price' => 5000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Đặt cọc lịch ngoài trời', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-12 16:00:00'],
                            ['title' => 'Thanh toán sau lễ cưới', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-25 14:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Lâm Ái Vy',
                        'phone' => '0945112233',
                        'date' => '2026-03-30 09:00:00',
                        'status' => 'completed',
                        'total' => 4000000,
                        'items' => [
                            ['name' => 'Makeup Lookbook BST Thời Trang Xuân Hè', 'price' => 4000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán Lookbook Xuân Hè', 'amount' => 4000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-03-30 17:00:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T3/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-03-02'],
                    ['name' => 'Bộ cọ chuyên nghiệp Picasso Master 24 cây', 'category' => 'Dụng cụ', 'amount' => 3800000, 'date' => '2026-03-04'],
                    ['name' => 'Bảng mắt Natasha Denona & Má hồng NARS', 'category' => 'Mỹ phẩm', 'amount' => 2700000, 'date' => '2026-03-11'],
                    ['name' => 'Tiền điện, nước & Wifi T3', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1200000, 'date' => '2026-03-10'],
                    ['name' => 'Quảng cáo Facebook & Tiktok Ads gói 8/3', 'category' => 'Marketing', 'amount' => 1500000, 'date' => '2026-03-05'],
                ],
            ],

            // THÁNG 4/2026 (Kỷ Yếu & Sự Kiện)
            4 => [
                'bookings' => [
                    [
                        'name' => 'Dương Thảo Linh',
                        'phone' => '0972334455',
                        'date' => '2026-04-12 06:30:00',
                        'status' => 'completed',
                        'total' => 6400000,
                        'items' => [
                            ['name' => 'Gói Makeup Kỷ Yếu Nhóm Sinh Viên (8 bạn)', 'price' => 6400000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc gói kỷ yếu', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-04-03 10:00:00'],
                            ['title' => 'Thanh toán nốt ngày chụp', 'amount' => 4400000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-04-12 11:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Cao Thùy Tiên',
                        'phone' => '0911223344',
                        'date' => '2026-04-18 08:00:00',
                        'status' => 'completed',
                        'total' => 5200000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Tiệc Cưới Khách Sạn 5 Sao', 'price' => 5200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc lịch cưới', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-04-06 14:00:00'],
                            ['title' => 'Thanh toán sau tiệc', 'amount' => 3200000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-04-18 13:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Tạ Kiều Oanh',
                        'phone' => '0987119922',
                        'date' => '2026-04-20 08:30:00',
                        'status' => 'completed',
                        'total' => 3800000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ăn Hỏi Phong Cách Hàn Quốc', 'price' => 3800000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc ăn hỏi', 'amount' => 1500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-04-10 11:30:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 2300000, 'method' => 'Tiền mặt', 'date' => '2026-04-20 12:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Vương Hải Yến',
                        'phone' => '0936998877',
                        'date' => '2026-04-22 15:00:00',
                        'status' => 'completed',
                        'total' => 3200000,
                        'items' => [
                            ['name' => 'Makeup MC Dẫn Chương Trình Lễ Hội', 'price' => 3200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán sự kiện', 'amount' => 3200000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-04-22 18:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Lương Mỹ Duyên',
                        'phone' => '0904556677',
                        'date' => '2026-04-26 09:30:00',
                        'status' => 'completed',
                        'total' => 4000000,
                        'items' => [
                            ['name' => 'Makeup Chụp Ảnh Nghệ Thuật Nàng Thơ', 'price' => 4000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán trọn gói concept', 'amount' => 4000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-04-26 15:00:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T4/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-04-02'],
                    ['name' => 'Xịt khóa nền Urban Decay & Skindinavia (4 chai)', 'category' => 'Mỹ phẩm', 'amount' => 2400000, 'date' => '2026-04-08'],
                    ['name' => 'Mi giả gân trong & keo dán mi DUO', 'category' => 'Dụng cụ', 'amount' => 1300000, 'date' => '2026-04-15'],
                    ['name' => 'Tiền điện, nước & Wifi T4', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1100000, 'date' => '2026-04-10'],
                    ['name' => 'Bảo dưỡng và bọc lại ghế trang điểm Studio', 'category' => 'Vận hành', 'amount' => 800000, 'date' => '2026-04-24'],
                ],
            ],

            // THÁNG 5/2026 (Cao Điểm Kỷ Yếu & Tiệc Hè)
            5 => [
                'bookings' => [
                    [
                        'name' => 'Quách Quỳnh Chi',
                        'phone' => '0981223344',
                        'date' => '2026-05-14 06:00:00',
                        'status' => 'completed',
                        'total' => 7500000,
                        'items' => [
                            ['name' => 'Makeup Kỷ Yếu Lớp 12 Chuyên (10 bạn)', 'price' => 7500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc gói kỷ yếu lớn', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-05-02 10:00:00'],
                            ['title' => 'Thanh toán nốt ngày chụp', 'amount' => 4500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-05-14 12:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Trần Gia Hân',
                        'phone' => '0933445566',
                        'date' => '2026-05-19 08:00:00',
                        'status' => 'completed',
                        'total' => 5500000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Đãi Tiệc Resort Sang Trọng', 'price' => 5500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc giữ lịch tiệc Resort', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-05-05 14:30:00'],
                            ['title' => 'Thanh toán nốt sau tiệc', 'amount' => 3500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-05-19 13:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Nguyễn Hoàng Kim',
                        'phone' => '0978990011',
                        'date' => '2026-05-21 07:30:00',
                        'status' => 'completed',
                        'total' => 4800000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Chụp Pre-wedding Ngoại Cảnh', 'price' => 4800000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc chụp Pre-wedding', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-05-08 09:30:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 2800000, 'method' => 'Tiền mặt', 'date' => '2026-05-21 16:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Phan Thùy Trang',
                        'phone' => '0912889900',
                        'date' => '2026-05-24 14:00:00',
                        'status' => 'completed',
                        'total' => 4200000,
                        'items' => [
                            ['name' => 'Makeup Khai Trương Thẩm Mỹ Viện', 'price' => 4200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán sự kiện khai trương', 'amount' => 4200000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-05-24 17:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Hồ Nhật Hạ',
                        'phone' => '0909554433',
                        'date' => '2026-05-27 09:00:00',
                        'status' => 'completed',
                        'total' => 4500000,
                        'items' => [
                            ['name' => 'Makeup Lookbook BST Thời Trang Hè', 'price' => 4500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán hợp đồng Lookbook Hè', 'amount' => 4500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-05-27 18:00:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T5/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-05-02'],
                    ['name' => 'Đèn Ring Light Neewer 18 inch & Chân đỡ', 'category' => 'Dụng cụ', 'amount' => 2800000, 'date' => '2026-05-07'],
                    ['name' => 'Kem lót kiềm dầu Danessa Myricks & Phấn Huda', 'category' => 'Mỹ phẩm', 'amount' => 2600000, 'date' => '2026-05-12'],
                    ['name' => 'Tiền điện điều hòa hè, nước & Wifi T5', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1500000, 'date' => '2026-05-10'],
                    ['name' => 'Quảng cáo Facebook Ads mùa Kỷ Yếu & Cô Dâu hè', 'category' => 'Marketing', 'amount' => 1000000, 'date' => '2026-05-04'],
                ],
            ],

            // THÁNG 6/2026
            6 => [
                'bookings' => [
                    [
                        'name' => 'Mai Thu Thảo',
                        'phone' => '0984112233',
                        'date' => '2026-06-15 08:00:00',
                        'status' => 'completed',
                        'total' => 6000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Tiệc Cưới Bãi Biển VIP', 'price' => 6000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc gói tiệc cưới', 'amount' => 2500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-06-03 11:00:00'],
                            ['title' => 'Thanh toán nốt sau tiệc', 'amount' => 3500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-06-15 13:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Đỗ Thùy Dung',
                        'phone' => '0917665544',
                        'date' => '2026-06-08 10:00:00',
                        'status' => 'completed',
                        'total' => 3500000,
                        'items' => [
                            ['name' => 'Makeup Chụp Ảnh Profile Giảng Viên', 'price' => 3500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán profile', 'amount' => 3500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-06-08 13:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Tống Hồng Hạnh',
                        'phone' => '0939887766',
                        'date' => '2026-06-20 08:30:00',
                        'status' => 'completed',
                        'total' => 4200000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ăn Hỏi & Gia Tiên', 'price' => 4200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc ăn hỏi', 'amount' => 1500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-06-10 15:00:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 2700000, 'method' => 'Tiền mặt', 'date' => '2026-06-20 12:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Kiều Mỹ Uyên',
                        'phone' => '0908119900',
                        'date' => '2026-06-23 09:00:00',
                        'status' => 'completed',
                        'total' => 4800000,
                        'items' => [
                            ['name' => 'Makeup Lookbook BST Du Lịch Hè', 'price' => 4800000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán Lookbook Hè', 'amount' => 4800000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-06-23 18:00:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T6/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-06-02'],
                    ['name' => 'Cốp trang điểm vali kéo chuyên nghiệp 4 bánh', 'category' => 'Dụng cụ', 'amount' => 2200000, 'date' => '2026-06-09'],
                    ['name' => 'Phấn bắt sáng Fenty Beauty & Kem lót Benefit', 'category' => 'Mỹ phẩm', 'amount' => 1600000, 'date' => '2026-06-16'],
                    ['name' => 'Tiền điện, nước & Wifi T6', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1400000, 'date' => '2026-06-10'],
                ],
            ],

            // THÁNG 7/2026
            7 => [
                'bookings' => [
                    [
                        'name' => 'Hoàng Yến Trang',
                        'phone' => '0976223344',
                        'date' => '2026-07-16 07:00:00',
                        'status' => 'completed',
                        'total' => 6500000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Chụp Pre-wedding Đà Lạt (2 Ngày)', 'price' => 6500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc gói Pre-wedding ngoại tỉnh', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-07-04 10:30:00'],
                            ['title' => 'Thanh toán hoàn tất', 'amount' => 3500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-07-16 18:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Triệu Diệu Hoa',
                        'phone' => '0915334455',
                        'date' => '2026-07-21 08:30:00',
                        'status' => 'completed',
                        'total' => 5500000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Tone Nâu Tây Sang Trọng', 'price' => 5500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc giữ ngày cưới', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-07-07 14:00:00'],
                            ['title' => 'Thanh toán nốt sau tiệc', 'amount' => 3500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-07-21 13:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Võ Trà My',
                        'phone' => '0931223388',
                        'date' => '2026-07-14 15:00:00',
                        'status' => 'completed',
                        'total' => 4500000,
                        'items' => [
                            ['name' => 'Makeup Sự Kiện Khai Trương Showroom', 'price' => 4500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán sự kiện Showroom', 'amount' => 4500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-07-14 18:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Đinh Hoài An',
                        'phone' => '0906778899',
                        'date' => '2026-07-19 10:00:00',
                        'status' => 'completed',
                        'total' => 3200000,
                        'items' => [
                            ['name' => 'Makeup Chụp Ảnh Cá Nhân Mừng Sinh Nhật', 'price' => 3200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán sinh nhật', 'amount' => 3200000, 'method' => 'Tiền mặt', 'date' => '2026-07-19 13:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Nguyễn Ngọc Diễm',
                        'phone' => '0989332211',
                        'date' => '2026-07-28 08:00:00',
                        'status' => 'completed',
                        'total' => 4000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ăn Hỏi Truyền Thống', 'price' => 4000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc ăn hỏi', 'amount' => 1500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-07-12 11:00:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 2500000, 'method' => 'Tiền mặt', 'date' => '2026-07-28 11:30:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T7/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-07-02'],
                    ['name' => 'Phụ kiện trang sức cài tóc cô dâu & vương miện', 'category' => 'Phụ kiện', 'amount' => 2500000, 'date' => '2026-07-08'],
                    ['name' => 'Set che khuyết điểm Kryolan & Triệt sắc MUFE', 'category' => 'Mỹ phẩm', 'amount' => 1800000, 'date' => '2026-07-17'],
                    ['name' => 'Tiền điện, nước & Wifi T7', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1300000, 'date' => '2026-07-10'],
                    ['name' => 'Nước giặt cọ Cinema Secrets & Bông tẩy trang', 'category' => 'Dụng cụ', 'amount' => 900000, 'date' => '2026-07-22'],
                ],
            ],

            // THÁNG 8/2026 (Chớm Mùa Cưới Thu Đông - Doanh Thu Tăng Mạnh)
            8 => [
                'bookings' => [
                    [
                        'name' => 'Trương Kim Chi',
                        'phone' => '0973445566',
                        'date' => '2026-08-15 07:00:00',
                        'status' => 'completed',
                        'total' => 7500000,
                        'items' => [
                            ['name' => 'Combo Makeup Cô Dâu VIP (Gia tiên + Tiệc tối)', 'price' => 7500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc gói VIP 2 buổi', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-02 09:30:00'],
                            ['title' => 'Thanh toán nốt sau tiệc cưới', 'amount' => 4500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-15 22:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Lê Thảo Nguyên',
                        'phone' => '0916554433',
                        'date' => '2026-08-18 08:30:00',
                        'status' => 'completed',
                        'total' => 5200000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ăn Hỏi & Mẹ Cô Dâu', 'price' => 5200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc ăn hỏi', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-05 14:00:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 3200000, 'method' => 'Tiền mặt', 'date' => '2026-08-18 12:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Hà Phương Linh',
                        'phone' => '0935667788',
                        'date' => '2026-08-10 09:00:00',
                        'status' => 'completed',
                        'total' => 6000000,
                        'items' => [
                            ['name' => 'Makeup Lookbook BST Váy Cưới Mùa Thu', 'price' => 6000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán hợp đồng Lookbook Váy Cưới', 'amount' => 6000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-10 18:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Đoàn Bích Thủy',
                        'phone' => '0907889900',
                        'date' => '2026-08-22 08:00:00',
                        'status' => 'completed',
                        'total' => 5000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Lễ Thành Hôn Tone Nude Tây', 'price' => 5000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc lịch cưới', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-08 10:00:00'],
                            ['title' => 'Thanh toán nốt sau tiệc', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-22 13:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Bùi Mai Khanh',
                        'phone' => '0982113355',
                        'date' => '2026-08-24 10:00:00',
                        'status' => 'completed',
                        'total' => 3500000,
                        'items' => [
                            ['name' => 'Makeup Chụp Ảnh Profile & Doanh Nghiệp', 'price' => 3500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán gói chụp profile', 'amount' => 3500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-24 14:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Phạm Thảo Ly',
                        'phone' => '0943221100',
                        'date' => '2026-08-29 07:30:00',
                        'status' => 'completed',
                        'total' => 4500000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Chụp Pre-wedding Phim Trường', 'price' => 4500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc phim trường', 'amount' => 1500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-14 11:30:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-08-29 17:00:00'],
                        ],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T8/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-08-02'],
                    ['name' => 'Set mỹ phẩm cao cấp mùa cưới (NARS, Laura Mercier)', 'category' => 'Mỹ phẩm', 'amount' => 4500000, 'date' => '2026-08-06'],
                    ['name' => 'Máy tạo kiểu tóc xoăn sóng chuyên nghiệp', 'category' => 'Dụng cụ', 'amount' => 3200000, 'date' => '2026-08-11'],
                    ['name' => 'Chạy quảng cáo Facebook & Google Ads Mùa Cưới', 'category' => 'Marketing', 'amount' => 1800000, 'date' => '2026-08-05'],
                    ['name' => 'Tiền điện, nước & Wifi T8', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1300000, 'date' => '2026-08-10'],
                ],
            ],

            // THÁNG 9/2026 (Tháng Hiện Tại - Cao Điểm Mùa Cưới)
            9 => [
                'bookings' => [
                    // Đơn hoàn thành 1
                    [
                        'name' => 'Nguyễn Thu Hằng',
                        'phone' => '0901234888',
                        'date' => '2026-09-10 08:00:00',
                        'status' => 'completed',
                        'total' => 5500000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Lễ Cưới Trung Tâm Tiệc Cưới', 'price' => 5500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Đặt cọc giữ lịch cưới', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-02 10:00:00'],
                            ['title' => 'Thanh toán nốt sau tiệc', 'amount' => 3500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-10 13:00:00'],
                        ],
                    ],
                    // Đơn hoàn thành 2
                    [
                        'name' => 'Lê Phương Thảo',
                        'phone' => '0912345999',
                        'date' => '2026-09-14 07:30:00',
                        'status' => 'completed',
                        'total' => 4800000,
                        'items' => [
                            ['name' => 'Combo Makeup Cô Dâu & Mẹ Cô Dâu', 'price' => 4800000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Cọc combo cưới', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-04 15:30:00'],
                            ['title' => 'Thanh toán nốt', 'amount' => 2800000, 'method' => 'Tiền mặt', 'date' => '2026-09-14 12:00:00'],
                        ],
                    ],
                    // Đơn hoàn thành 3
                    [
                        'name' => 'Trần Bảo Ngọc',
                        'phone' => '0988665544',
                        'date' => '2026-09-18 09:00:00',
                        'status' => 'completed',
                        'total' => 4200000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Chụp Ảnh Cưới Studio', 'price' => 4200000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán trọn gói chụp studio', 'amount' => 4200000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-18 16:00:00'],
                        ],
                    ],
                    // Đơn hoàn thành 4
                    [
                        'name' => 'Đặng Hoàng Kim',
                        'phone' => '0977221199',
                        'date' => '2026-09-20 15:30:00',
                        'status' => 'completed',
                        'total' => 3000000,
                        'items' => [
                            ['name' => 'Makeup Đi Tiệc Gala Trung Thu & Sự Kiện', 'price' => 3000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Thanh toán tiệc gala', 'amount' => 3000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-20 18:00:00'],
                        ],
                    ],

                    // Đơn đã xác nhận (Confirmed) - Đã cọc một phần, còn công nợ cần thu
                    [
                        'name' => 'Vũ Quỳnh Trang',
                        'phone' => '0966443322',
                        'date' => '2026-09-27 07:00:00',
                        'status' => 'confirmed',
                        'total' => 6500000,
                        'items' => [
                            ['name' => 'Combo Makeup Cô Dâu VIP 2 Buổi (Hôn lễ + Tiệc tối)', 'price' => 6500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Đặt cọc giữ lịch hôn lễ ngày 27/09', 'amount' => 2500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-12 11:00:00'],
                        ],
                    ],
                    [
                        'name' => 'Phạm Mai Anh',
                        'phone' => '0933119988',
                        'date' => '2026-09-28 08:30:00',
                        'status' => 'confirmed',
                        'total' => 4500000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ăn Hỏi & 2 Phù Dâu', 'price' => 4500000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Đặt cọc ăn hỏi 28/09', 'amount' => 1500000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-16 16:30:00'],
                        ],
                    ],
                    [
                        'name' => 'Đỗ Bích Trâm',
                        'phone' => '0908334455',
                        'date' => '2026-09-30 07:30:00',
                        'status' => 'confirmed',
                        'total' => 6000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Chụp Ảnh Pre-wedding Ngoại Cảnh', 'price' => 6000000, 'qty' => 1],
                        ],
                        'payments' => [
                            ['title' => 'Đặt cọc gói chụp 30/09', 'amount' => 2000000, 'method' => 'Chuyển khoản ngân hàng', 'date' => '2026-09-21 14:00:00'],
                        ],
                    ],

                    // Đơn chờ xử lý (Pending) - Khách mới đặt qua website cần Admin xác nhận
                    [
                        'name' => 'Nguyễn Khánh Ly',
                        'phone' => '0944112233',
                        'date' => '2026-09-29 08:00:00',
                        'status' => 'pending',
                        'total' => 5000000,
                        'items' => [
                            ['name' => 'Makeup Cô Dâu Ngày Cưới Cao Cấp', 'price' => 5000000, 'qty' => 1],
                        ],
                        'payments' => [],
                    ],
                    [
                        'name' => 'Hoàng Thanh Thảo',
                        'phone' => '0971223344',
                        'date' => '2026-09-26 16:00:00',
                        'status' => 'pending',
                        'total' => 1500000,
                        'items' => [
                            ['name' => 'Makeup Đi Tiệc Sinh Nhật & Sự Kiện Tối', 'price' => 1500000, 'qty' => 1],
                        ],
                        'payments' => [],
                    ],
                ],
                'expenses' => [
                    ['name' => 'Tiền thuê mặt bằng Studio T9/2026', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 6000000, 'date' => '2026-09-02'],
                    ['name' => 'Mi giả cao cấp gân trong & bông mút trang điểm', 'category' => 'Dụng cụ', 'amount' => 1600000, 'date' => '2026-09-07'],
                    ['name' => 'Tiền điện, nước & Wifi T9', 'category' => 'Mặt bằng & Tiện ích', 'amount' => 1300000, 'date' => '2026-09-10'],
                    ['name' => 'Chạy quảng cáo Facebook Ads khuyến mãi mùa cưới T9', 'category' => 'Marketing', 'amount' => 1500000, 'date' => '2026-09-05'],
                ],
            ],
        ];

        // -----------------------------------------------------------------------------
        // TIẾN HÀNH TẠO RECORD VÀO CƠ SỞ DỮ LIỆU
        // -----------------------------------------------------------------------------

        foreach ($monthlyData as $month => $data) {
            // 1. Tạo Bookings, Booking Items và Payments
            foreach ($data['bookings'] as $b) {
                $booking = Booking::create([
                    'customer_name' => $b['name'],
                    'phone' => $b['phone'],
                    'social_link' => 'https://facebook.com',
                    'service_ids' => [1],
                    'booking_date' => Carbon::parse($b['date']),
                    'message' => 'Yêu cầu tư vấn phong cách nhẹ nhàng, sang trọng, tôn da.',
                    'status' => $b['status'],
                    'total_amount' => $b['total'],
                    'notes' => 'Khách hàng đặt dịch vụ makeup chuyên nghiệp.',
                    'created_at' => Carbon::parse($b['date'])->subDays(rand(5, 12)),
                    'updated_at' => Carbon::parse($b['date']),
                ]);

                foreach ($b['items'] as $item) {
                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'service_name' => $item['name'],
                        'service_date' => Carbon::parse($b['date']),
                        'description' => 'Trang điểm & làm tóc hoàn chỉnh cao cấp',
                        'price' => $item['price'],
                        'quantity' => $item['qty'],
                    ]);
                }

                foreach ($b['payments'] as $p) {
                    Payment::create([
                        'booking_id' => $booking->id,
                        'title' => $p['title'],
                        'amount' => $p['amount'],
                        'payment_method' => $p['method'],
                        'proof_image' => null,
                        'payment_date' => Carbon::parse($p['date']),
                        'notes' => 'Đã xác nhận tiền về tài khoản ngân hàng.',
                        'created_at' => Carbon::parse($p['date']),
                        'updated_at' => Carbon::parse($p['date']),
                    ]);
                }
            }

            // 2. Tạo Expenses
            foreach ($data['expenses'] as $e) {
                Expense::create([
                    'item_name' => $e['name'],
                    'category' => $e['category'],
                    'amount' => $e['amount'],
                    'expense_date' => Carbon::parse($e['date']),
                    'product_image' => null,
                    'receipt_image' => null,
                    'buy_link' => 'https://shopee.vn',
                    'notes' => 'Chi phí vận hành và vật tư phục vụ studio.',
                    'created_at' => Carbon::parse($e['date']),
                    'updated_at' => Carbon::parse($e['date']),
                ]);
            }
        }
    }
}
