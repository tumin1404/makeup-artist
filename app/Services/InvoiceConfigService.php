<?php

namespace App\Services;

use App\Models\Setting;

class InvoiceConfigService
{
    const SETTING_KEY = 'invoice_custom_config';

    /**
     * Cấu trúc Khối (Blocks) và Phần tử con (Elements) 2 tầng chuẩn
     */
    public static function getDefaultBlocksStructure(): array
    {
        return [
            'block_seller' => [
                'id' => 'block_seller',
                'name' => 'Khối Thông Tin Studio / Người Bán',
                'width' => 'half',
                'align' => 'left',
                'elements_order' => ['seller_logo', 'seller_name', 'seller_phone', 'seller_address', 'seller_tax', 'seller_bank'],
                'elements' => [
                    'seller_logo' => ['id' => 'seller_logo', 'name' => 'Logo Studio', 'align' => 'left', 'size' => 'md', 'visible' => true],
                    'seller_name' => ['id' => 'seller_name', 'name' => 'Tên Studio / Đơn vị', 'align' => 'left', 'size' => 'md', 'visible' => true],
                    'seller_phone' => ['id' => 'seller_phone', 'name' => 'Hotline / Số điện thoại', 'align' => 'left', 'size' => 'sm', 'visible' => true],
                    'seller_address' => ['id' => 'seller_address', 'name' => 'Địa chỉ cơ sở', 'align' => 'left', 'size' => 'sm', 'visible' => true],
                    'seller_tax' => ['id' => 'seller_tax', 'name' => 'Mã số thuế bên bán', 'align' => 'left', 'size' => 'sm', 'visible' => false],
                    'seller_bank' => ['id' => 'seller_bank', 'name' => 'STK & Ngân hàng', 'align' => 'left', 'size' => 'sm', 'visible' => true],
                ],
            ],
            'block_invoice_meta' => [
                'id' => 'block_invoice_meta',
                'name' => 'Khối Tiêu Đề & Thông Tin Hóa Đơn',
                'width' => 'half',
                'align' => 'right',
                'elements_order' => ['invoice_title', 'invoice_subtitle', 'invoice_number', 'invoice_date', 'invoice_symbol', 'invoice_cqt'],
                'elements' => [
                    'invoice_title' => ['id' => 'invoice_title', 'name' => 'Tiêu đề hóa đơn', 'align' => 'right', 'size' => 'lg', 'visible' => true],
                    'invoice_subtitle' => ['id' => 'invoice_subtitle', 'name' => 'Phụ đề / Mẫu biểu', 'align' => 'right', 'size' => 'sm', 'visible' => true],
                    'invoice_number' => ['id' => 'invoice_number', 'name' => 'Số hóa đơn (#00012)', 'align' => 'right', 'size' => 'sm', 'visible' => true],
                    'invoice_date' => ['id' => 'invoice_date', 'name' => 'Ngày lập hóa đơn', 'align' => 'right', 'size' => 'sm', 'visible' => true],
                    'invoice_symbol' => ['id' => 'invoice_symbol', 'name' => 'Ký hiệu mẫu số HĐ', 'align' => 'right', 'size' => 'sm', 'visible' => false],
                    'invoice_cqt' => ['id' => 'invoice_cqt', 'name' => 'Mã cơ quan thuế', 'align' => 'right', 'size' => 'sm', 'visible' => false],
                ],
            ],
            'block_buyer' => [
                'id' => 'block_buyer',
                'name' => 'Khối Thông Tin Khách Hàng (Bên Mua)',
                'width' => 'full',
                'align' => 'left',
                'elements_order' => ['buyer_name', 'buyer_phone', 'buyer_address', 'buyer_company', 'buyer_tax', 'buyer_payment'],
                'elements' => [
                    'buyer_name' => ['id' => 'buyer_name', 'name' => 'Họ tên khách hàng', 'align' => 'left', 'size' => 'md', 'visible' => true],
                    'buyer_phone' => ['id' => 'buyer_phone', 'name' => 'Số điện thoại khách', 'align' => 'left', 'size' => 'md', 'visible' => true],
                    'buyer_address' => ['id' => 'buyer_address', 'name' => 'Địa chỉ khách hàng', 'align' => 'left', 'size' => 'md', 'visible' => true],
                    'buyer_company' => ['id' => 'buyer_company', 'name' => 'Tên công ty / Đơn vị mua', 'align' => 'left', 'size' => 'md', 'visible' => false],
                    'buyer_tax' => ['id' => 'buyer_tax', 'name' => 'Mã số thuế khách hàng', 'align' => 'left', 'size' => 'md', 'visible' => false],
                    'buyer_payment' => ['id' => 'buyer_payment', 'name' => 'Hình thức thanh toán (TM/CK)', 'align' => 'left', 'size' => 'md', 'visible' => true],
                ],
            ],
            'block_items_table' => [
                'id' => 'block_items_table',
                'name' => 'Khối Bảng Kê Chi Tiết Dịch Vụ',
                'width' => 'full',
                'align' => 'left',
                'elements_order' => ['items_table_content'],
                'elements' => [
                    'items_table_content' => ['id' => 'items_table_content', 'name' => 'Bảng danh sách dịch vụ / sản phẩm', 'align' => 'left', 'size' => 'md', 'visible' => true],
                ],
            ],
            'block_vietqr' => [
                'id' => 'block_vietqr',
                'name' => 'Khối VietQR Chuyển Khoản',
                'width' => 'half',
                'align' => 'left',
                'elements_order' => ['vietqr_box'],
                'elements' => [
                    'vietqr_box' => ['id' => 'vietqr_box', 'name' => 'Mã QR & Thông tin tài khoản', 'align' => 'left', 'size' => 'md', 'visible' => true],
                ],
            ],
            'block_totals' => [
                'id' => 'block_totals',
                'name' => 'Khối Tổng Kết Tiền & Đặt Cọc',
                'width' => 'half',
                'align' => 'right',
                'elements_order' => ['subtotal_row', 'tax_row', 'deposit_row', 'remaining_row', 'words_row'],
                'elements' => [
                    'subtotal_row' => ['id' => 'subtotal_row', 'name' => 'Tổng tiền hàng hóa', 'align' => 'right', 'size' => 'md', 'visible' => true],
                    'tax_row' => ['id' => 'tax_row', 'name' => 'Thuế GTGT (VAT)', 'align' => 'right', 'size' => 'sm', 'visible' => false],
                    'deposit_row' => ['id' => 'deposit_row', 'name' => 'Đã đặt cọc', 'align' => 'right', 'size' => 'sm', 'visible' => true],
                    'remaining_row' => ['id' => 'remaining_row', 'name' => 'Còn lại phải thu', 'align' => 'right', 'size' => 'md', 'visible' => true],
                    'words_row' => ['id' => 'words_row', 'name' => 'Số tiền viết bằng chữ', 'align' => 'left', 'size' => 'sm', 'visible' => true],
                ],
            ],
            'block_notes' => [
                'id' => 'block_notes',
                'name' => 'Khối Ghi Chú, Lưu Ý & Lời Cảm Ơn',
                'width' => 'full',
                'align' => 'left',
                'elements_order' => ['notes_text', 'thank_you_text'],
                'elements' => [
                    'notes_text' => ['id' => 'notes_text', 'name' => 'Nội dung ghi chú / Lưu ý', 'align' => 'left', 'size' => 'sm', 'visible' => true],
                    'thank_you_text' => ['id' => 'thank_you_text', 'name' => 'Lời cảm ơn chân trang', 'align' => 'center', 'size' => 'sm', 'visible' => true],
                ],
            ],
            'block_signatures' => [
                'id' => 'block_signatures',
                'name' => 'Khối Chữ Ký Trách Nhiệm',
                'width' => 'full',
                'align' => 'center',
                'elements_order' => ['signature_customer_part', 'signature_creator_part'],
                'elements' => [
                    'signature_customer_part' => ['id' => 'signature_customer_part', 'name' => 'Chữ ký Khách hàng', 'align' => 'center', 'size' => 'md', 'visible' => true],
                    'signature_creator_part' => ['id' => 'signature_creator_part', 'name' => 'Chữ ký Người lập phiếu / Studio', 'align' => 'center', 'size' => 'md', 'visible' => true],
                ],
            ],
            'block_lookup' => [
                'id' => 'block_lookup',
                'name' => 'Khối Tra Cứu Hóa Đơn Điện Tử',
                'width' => 'full',
                'align' => 'center',
                'elements_order' => ['lookup_content'],
                'elements' => [
                    'lookup_content' => ['id' => 'lookup_content', 'name' => 'Link tra cứu HĐĐT & Mã bảo mật', 'align' => 'center', 'size' => 'sm', 'visible' => false],
                ],
            ],
        ];
    }

    /**
     * Danh sách 4 Mẫu Preset cấu hình sẵn
     */
    public static function getPresets(): array
    {
        $defaultBlocks = self::getDefaultBlocksStructure();

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
                    'blocks_structure' => $defaultBlocks,
                    'blocks_order' => array_keys($defaultBlocks),
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
                    'blocks_structure' => $defaultBlocks,
                    'blocks_order' => array_keys($defaultBlocks),
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
                    'blocks_structure' => $defaultBlocks,
                    'blocks_order' => array_keys($defaultBlocks),
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
                    'blocks_structure' => $defaultBlocks,
                    'blocks_order' => array_keys($defaultBlocks),
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

        // Đảm bảo cấu trúc blocks_structure luôn đầy đủ
        $defaultBlocks = self::getDefaultBlocksStructure();
        if (empty($decoded['blocks_structure'])) {
            $decoded['blocks_structure'] = $defaultBlocks;
        } else {
            // Merge các block mới nếu có
            foreach ($defaultBlocks as $bId => $bVal) {
                if (!isset($decoded['blocks_structure'][$bId])) {
                    $decoded['blocks_structure'][$bId] = $bVal;
                } else {
                    // Merge elements
                    foreach ($bVal['elements'] as $eId => $eVal) {
                        if (!isset($decoded['blocks_structure'][$bId]['elements'][$eId])) {
                            $decoded['blocks_structure'][$bId]['elements'][$eId] = $eVal;
                        }
                    }
                    if (empty($decoded['blocks_structure'][$bId]['elements_order'])) {
                        $decoded['blocks_structure'][$bId]['elements_order'] = $bVal['elements_order'];
                    }
                }
            }
        }

        if (empty($decoded['blocks_order'])) {
            $decoded['blocks_order'] = array_keys($defaultBlocks);
        }

        return array_merge($default, $decoded);
    }

    /**
     * Lưu cấu hình mới
     */
    public static function saveConfig(array $config): bool
    {
        return Setting::set(self::SETTING_KEY, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 'banking', 'text', 'Cấu hình mẫu hóa đơn và kéo thả phần tử 2 tầng');
    }
}
