<?php

namespace App\Services;

use App\Models\Setting;

class InvoiceConfigService
{
    const SETTING_KEY = 'invoice_custom_config';

    /**
     * Danh sách các khối giao diện có thể kéo thả, căn chỉnh vị trí và kích thước
     */
    public static function getAvailableBlocks(): array
    {
        return [
            'seller_header' => [
                'id' => 'seller_header',
                'name' => '1. Thông tin Đơn vị Bán hàng & Logo',
                'description' => 'Logo, Tên Studio/Công ty, MST, Địa chỉ, Hotline, STK',
                'icon' => 'heroicon-o-building-storefront',
                'default_align' => 'between',
                'default_width' => 'full',
                'default_size' => 'md',
            ],
            'invoice_meta' => [
                'id' => 'invoice_meta',
                'name' => '2. Tiêu đề Hóa đơn & Pháp lý',
                'description' => 'Tiêu đề hóa đơn, Số HĐ, Ký hiệu mẫu số, Ngày lập, Mã CQT',
                'icon' => 'heroicon-o-document-text',
                'default_align' => 'center',
                'default_width' => 'full',
                'default_size' => 'md',
            ],
            'buyer_info' => [
                'id' => 'buyer_info',
                'name' => '3. Thông tin Khách hàng (Bên mua)',
                'description' => 'Tên khách hàng, Tên công ty, MST khách, SĐT, Địa chỉ, Hình thức TT',
                'icon' => 'heroicon-o-user',
                'default_align' => 'left',
                'default_width' => 'full',
                'default_size' => 'md',
            ],
            'items_table' => [
                'id' => 'items_table',
                'name' => '4. Bảng Kê Dịch vụ / Sản phẩm',
                'description' => 'STT, Tên dịch vụ/hàng, ĐVT, SL, Đơn giá, Chiết khấu, Thuế, Thành tiền',
                'icon' => 'heroicon-o-table-cells',
                'default_align' => 'left',
                'default_width' => 'full',
                'default_size' => 'md',
            ],
            'summary_totals' => [
                'id' => 'summary_totals',
                'name' => '5. Tổng Tiền, Thuế VAT & Tiền bằng chữ',
                'description' => 'Cộng tiền hàng, Thuế GTGT, Chiết khấu, Đặt cọc, Còn lại, Đọc số tiền bằng chữ',
                'icon' => 'heroicon-o-calculator',
                'default_align' => 'right',
                'default_width' => 'half',
                'default_size' => 'md',
            ],
            'vietqr_banking' => [
                'id' => 'vietqr_banking',
                'name' => '6. Thanh toán VietQR & Ngân hàng',
                'description' => 'Mã VietQR động tự nạp số tiền + cú pháp CK và thông tin tài khoản',
                'icon' => 'heroicon-o-qr-code',
                'default_align' => 'left',
                'default_width' => 'half',
                'default_size' => 'md',
            ],
            'notes_terms' => [
                'id' => 'notes_terms',
                'name' => '7. Ghi chú, Lưu ý & Quy định đổi trả/bảo hành',
                'description' => 'Lời dặn dò khách hàng, chính sách đặt cọc, đổi trả hoặc điều kiện xuất VAT',
                'icon' => 'heroicon-o-chat-bubble-left-ellipsis',
                'default_align' => 'left',
                'default_width' => 'full',
                'default_size' => 'md',
            ],
            'signatures' => [
                'id' => 'signatures',
                'name' => '8. Khu vực Chữ Ký & Dấu Chứng Thực',
                'description' => 'Chữ ký 2 bên (Khách - Người lập), 5 bên (Kho), hoặc Dấu điện tử Signature Valid',
                'icon' => 'heroicon-o-pencil-square',
                'default_align' => 'center',
                'default_width' => 'full',
                'default_size' => 'md',
            ],
            'footer_lookup' => [
                'id' => 'footer_lookup',
                'name' => '9. Chân trang Tra cứu & Bản quyền',
                'description' => 'Link tra cứu hóa đơn trực tuyến, mã bí mật, đơn vị cung cấp giải pháp',
                'icon' => 'heroicon-o-globe-alt',
                'default_align' => 'center',
                'default_width' => 'full',
                'default_size' => 'md',
            ],
        ];
    }

    /**
     * Danh sách 4 Mẫu Preset cấu hình sẵn
     */
    public static function getPresets(): array
    {
        return [
            'luxury_service' => [
                'id' => 'luxury_service',
                'name' => '🌟 Phiếu Dịch Vụ & Makeup Luxury',
                'badge' => 'Phổ biến nhất',
                'color' => 'amber',
                'description' => 'Tone màu Gold & Dark sang trọng, hiển thị lịch trình chi tiết, tích hợp mã VietQR quét chuyển khoản tức thì.',
                'config' => [
                    'paper_size' => 'a4',
                    'color_theme' => 'luxury',
                    'invoice_title' => 'PHIẾU DỊCH VỤ & THANH TOÁN',
                    'invoice_subtitle' => 'Makeup Artist & Beauty Studio',
                    'show_logo' => true,
                    'show_seller_name' => true,
                    'show_seller_tax' => false,
                    'show_seller_address' => true,
                    'show_seller_phone' => true,
                    'show_seller_bank' => true,
                    'show_invoice_number' => true,
                    'invoice_number_prefix' => 'HD',
                    'show_invoice_symbol' => false,
                    'invoice_symbol' => '1C26TAV',
                    'show_cqt_code' => false,
                    'cqt_code' => '',
                    'show_buyer_name' => true,
                    'show_buyer_company' => false,
                    'show_buyer_tax' => false,
                    'show_buyer_phone' => true,
                    'show_buyer_address' => true,
                    'show_payment_method' => true,
                    'payment_method_default' => 'Tiền mặt / Chuyển khoản',
                    'show_column_code' => false,
                    'show_column_unit' => true,
                    'show_column_discount' => false,
                    'show_column_tax' => false,
                    'show_column_schedule' => true,
                    'tax_rate_default' => 0,
                    'show_tax_summary' => false,
                    'show_deposit' => true,
                    'show_debt' => false,
                    'show_amount_in_words' => true,
                    'show_vietqr' => true,
                    'show_notes' => true,
                    'notes_content' => "• Quý khách vui lòng kiểm tra diện mạo và phụ kiện trước khi rời studio.\n• Lịch hẹn trang điểm vui lòng có mặt đúng giờ để đảm bảo tiến độ.",
                    'footer_thank_you' => 'Cảm ơn quý khách đã tin tưởng và lựa chọn dịch vụ của chúng tôi!',
                    'signature_type' => 'two_parties',
                    'show_lookup_link' => false,
                    'lookup_url' => '',
                    'elements_layout' => [
                        'seller_header' => ['align' => 'between', 'width' => 'full', 'size' => 'md'],
                        'invoice_meta' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'buyer_info' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'items_table' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'summary_totals' => ['align' => 'right', 'width' => 'half', 'size' => 'md'],
                        'vietqr_banking' => ['align' => 'left', 'width' => 'half', 'size' => 'md'],
                        'notes_terms' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'signatures' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'footer_lookup' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                    ],
                    'block_order' => [
                        'seller_header',
                        'invoice_meta',
                        'buyer_info',
                        'items_table',
                        'vietqr_banking',
                        'summary_totals',
                        'notes_terms',
                        'signatures',
                    ],
                ]
            ],
            'retail_pos' => [
                'id' => 'retail_pos',
                'name' => '🏬 Hóa Đơn Bán Hàng & Bán Lẻ (POS)',
                'badge' => 'Cửa hàng / Bán mỹ phẩm',
                'color' => 'blue',
                'description' => 'Mẫu hóa đơn bán lẻ mỹ phẩm, dụng cụ có chiết khấu, theo dõi tiền cọc, nợ cũ và tổng công nợ.',
                'config' => [
                    'paper_size' => 'a4',
                    'color_theme' => 'classic',
                    'invoice_title' => 'HÓA ĐƠN BÁN HÀNG',
                    'invoice_subtitle' => 'Chuyên Cung Cấp Mỹ Phẩm & Dịch Vụ Làm Đẹp',
                    'show_logo' => true,
                    'show_seller_name' => true,
                    'show_seller_tax' => true,
                    'show_seller_address' => true,
                    'show_seller_phone' => true,
                    'show_seller_bank' => true,
                    'show_invoice_number' => true,
                    'invoice_number_prefix' => 'HD',
                    'show_invoice_symbol' => false,
                    'invoice_symbol' => 'BH26',
                    'show_cqt_code' => false,
                    'cqt_code' => '',
                    'show_buyer_name' => true,
                    'show_buyer_company' => false,
                    'show_buyer_tax' => false,
                    'show_buyer_phone' => true,
                    'show_buyer_address' => true,
                    'show_payment_method' => true,
                    'payment_method_default' => 'Tiền mặt',
                    'show_column_code' => true,
                    'show_column_unit' => true,
                    'show_column_discount' => true,
                    'show_column_tax' => false,
                    'show_column_schedule' => false,
                    'tax_rate_default' => 0,
                    'show_tax_summary' => false,
                    'show_deposit' => true,
                    'show_debt' => true,
                    'show_amount_in_words' => true,
                    'show_vietqr' => true,
                    'show_notes' => true,
                    'notes_content' => "Quý khách vui lòng kiểm tra hàng hoá trước khi ra khỏi cửa hàng. Hàng đã mua không đổi trả sau 48h. Trân trọng!",
                    'footer_thank_you' => 'Kính chúc Quý khách luôn rạng rỡ và thành công!',
                    'signature_type' => 'two_parties',
                    'show_lookup_link' => false,
                    'lookup_url' => '',
                    'elements_layout' => [
                        'seller_header' => ['align' => 'between', 'width' => 'full', 'size' => 'md'],
                        'invoice_meta' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'buyer_info' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'items_table' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'summary_totals' => ['align' => 'right', 'width' => 'half', 'size' => 'md'],
                        'vietqr_banking' => ['align' => 'left', 'width' => 'half', 'size' => 'md'],
                        'notes_terms' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'signatures' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'footer_lookup' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                    ],
                    'block_order' => [
                        'seller_header',
                        'invoice_meta',
                        'buyer_info',
                        'items_table',
                        'vietqr_banking',
                        'summary_totals',
                        'notes_terms',
                        'signatures',
                    ],
                ]
            ],
            'electronic_vat' => [
                'id' => 'electronic_vat',
                'name' => '🏛️ Hóa Đơn Điện Tử Giá Trị Gia Tăng (VAT)',
                'badge' => 'Chuẩn NĐ 123 / TT 78',
                'color' => 'emerald',
                'description' => 'Mẫu hóa đơn tài chính chuẩn Tổng cục Thuế, có Ký hiệu mẫu số, Ký hiệu HĐ, Mã CQT, Bảng thuế suất 8%/10% và Dấu điện tử Signature Valid.',
                'config' => [
                    'paper_size' => 'a4',
                    'color_theme' => 'corporate',
                    'invoice_title' => 'HÓA ĐƠN GIÁ TRỊ GIA TĂNG',
                    'invoice_subtitle' => '',
                    'show_logo' => true,
                    'show_seller_name' => true,
                    'show_seller_tax' => true,
                    'show_seller_address' => true,
                    'show_seller_phone' => true,
                    'show_seller_bank' => true,
                    'show_invoice_number' => true,
                    'invoice_number_prefix' => '',
                    'show_invoice_symbol' => true,
                    'invoice_symbol' => '1C26TAV',
                    'show_cqt_code' => true,
                    'cqt_code' => '003447FDA6C1054E3CBBBB4A4C5AE4D9FF',
                    'show_buyer_name' => true,
                    'show_buyer_company' => true,
                    'show_buyer_tax' => true,
                    'show_buyer_phone' => true,
                    'show_buyer_address' => true,
                    'show_payment_method' => true,
                    'payment_method_default' => 'TM/CK',
                    'show_column_code' => true,
                    'show_column_unit' => true,
                    'show_column_discount' => false,
                    'show_column_tax' => true,
                    'show_column_schedule' => false,
                    'tax_rate_default' => 8,
                    'show_tax_summary' => true,
                    'show_deposit' => false,
                    'show_debt' => false,
                    'show_amount_in_words' => true,
                    'show_vietqr' => true,
                    'show_notes' => false,
                    'notes_content' => '',
                    'footer_thank_you' => '',
                    'signature_type' => 'digital_stamp',
                    'show_lookup_link' => true,
                    'lookup_url' => 'https://tracuu.hoadondientu.gdt.gov.vn',
                    'elements_layout' => [
                        'seller_header' => ['align' => 'between', 'width' => 'full', 'size' => 'md'],
                        'invoice_meta' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'buyer_info' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'items_table' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'summary_totals' => ['align' => 'right', 'width' => 'full', 'size' => 'md'],
                        'signatures' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'vietqr_banking' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'footer_lookup' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                    ],
                    'block_order' => [
                        'seller_header',
                        'invoice_meta',
                        'buyer_info',
                        'items_table',
                        'summary_totals',
                        'signatures',
                        'vietqr_banking',
                        'footer_lookup',
                    ],
                ]
            ],
            'inventory_voucher' => [
                'id' => 'inventory_voucher',
                'name' => '📦 Phiếu Xuất Kho / Nhập Kho Hàng Hóa',
                'badge' => 'Chuẩn TT 99/2025/TT-BTC',
                'color' => 'purple',
                'description' => 'Mẫu chứng từ quản lý kho vật tư mỹ phẩm chuẩn Thông tư 99/2025/TT-BTC và TT 200, có định khoản Nợ/Có và 5 chữ ký trách nhiệm.',
                'config' => [
                    'paper_size' => 'a4',
                    'color_theme' => 'classic',
                    'invoice_title' => 'PHIẾU XUẤT KHO BÁN HÀNG',
                    'invoice_subtitle' => 'Mẫu số 02 - VT (Kèm theo TT số 99/2025/TT-BTC)',
                    'show_logo' => true,
                    'show_seller_name' => true,
                    'show_seller_tax' => true,
                    'show_seller_address' => true,
                    'show_seller_phone' => false,
                    'show_seller_bank' => false,
                    'show_invoice_number' => true,
                    'invoice_number_prefix' => 'XK',
                    'show_invoice_symbol' => true,
                    'invoice_symbol' => 'VT02/2026',
                    'show_cqt_code' => false,
                    'cqt_code' => '',
                    'show_buyer_name' => true,
                    'show_buyer_company' => true,
                    'show_buyer_tax' => false,
                    'show_buyer_phone' => true,
                    'show_buyer_address' => true,
                    'show_payment_method' => false,
                    'payment_method_default' => '',
                    'show_column_code' => true,
                    'show_column_unit' => true,
                    'show_column_discount' => true,
                    'show_column_tax' => true,
                    'show_column_schedule' => false,
                    'tax_rate_default' => 8,
                    'show_tax_summary' => true,
                    'show_deposit' => false,
                    'show_debt' => false,
                    'show_amount_in_words' => true,
                    'show_vietqr' => false,
                    'show_notes' => true,
                    'notes_content' => 'Xuất bán hàng thu tiền ngay theo thỏa thuận dịch vụ.',
                    'footer_thank_you' => '',
                    'signature_type' => 'five_parties',
                    'show_lookup_link' => false,
                    'lookup_url' => '',
                    'elements_layout' => [
                        'seller_header' => ['align' => 'between', 'width' => 'full', 'size' => 'md'],
                        'invoice_meta' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                        'buyer_info' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'items_table' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'summary_totals' => ['align' => 'right', 'width' => 'full', 'size' => 'md'],
                        'notes_terms' => ['align' => 'left', 'width' => 'full', 'size' => 'md'],
                        'signatures' => ['align' => 'center', 'width' => 'full', 'size' => 'md'],
                    ],
                    'block_order' => [
                        'seller_header',
                        'invoice_meta',
                        'buyer_info',
                        'items_table',
                        'summary_totals',
                        'notes_terms',
                        'signatures',
                    ],
                ]
            ],
        ];
    }

    /**
     * Lấy cấu hình hóa đơn hiện tại trong database (kèm fallback mẫu luxury)
     */
    public static function getCurrentConfig(): array
    {
        $raw = Setting::get(self::SETTING_KEY);
        $default = self::getPresets()['luxury_service']['config'];
        $default['active_preset'] = 'luxury_service';

        if (empty($raw)) {
            return $default;
        }

        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($decoded)) {
            return $default;
        }

        return array_merge($default, $decoded);
    }

    /**
     * Lưu cấu hình mới
     */
    public static function saveConfig(array $config): bool
    {
        return Setting::set(self::SETTING_KEY, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 'banking', 'text', 'Cấu hình mẫu hóa đơn và kéo thả phần tử');
    }
}
