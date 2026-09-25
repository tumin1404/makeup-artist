<?php

namespace App\Helpers;

class InvoiceHelper
{
    /**
     * Chuyển đổi số tiền thành chữ tiếng Việt chuẩn quy định kế toán.
     *
     * @param float|int|string $number
     * @return string
     */
    public static function docTienBangChu($number): string
    {
        $number = floatval(preg_replace('/[^0-9.]/', '', (string) $number));
        if ($number <= 0) {
            return 'Không đồng.';
        }

        $digits = [
            'không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'
        ];

        $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];

        $numberStr = number_format($number, 0, '', '');
        $length = strlen($numberStr);
        $groups = [];

        // Chia số thành từng nhóm 3 chữ số từ phải sang trái
        while ($length > 0) {
            $take = min(3, $length);
            $start = $length - $take;
            $groups[] = substr($numberStr, $start, $take);
            $length -= $take;
        }

        $textGroups = [];
        $totalGroups = count($groups);

        for ($i = $totalGroups - 1; $i >= 0; $i--) {
            $groupVal = intval($groups[$i]);
            if ($groupVal === 0 && $totalGroups > 1) {
                continue;
            }

            $groupText = self::readThreeDigits($groups[$i], $digits, ($i < $totalGroups - 1));
            if (!empty($groupText)) {
                $unit = $units[$i] ?? '';
                $textGroups[] = trim($groupText . ' ' . $unit);
            }
        }

        $result = implode(' ', $textGroups);
        $result = preg_replace('/\s+/', ' ', trim($result));
        
        // Viết hoa chữ cái đầu tiên
        $result = mb_strtoupper(mb_substr($result, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($result, 1, null, 'UTF-8');
        
        return $result . ' đồng chẵn.';
    }

    /**
     * Đọc nhóm 3 chữ số.
     */
    private static function readThreeDigits(string $triplet, array $digits, bool $needHundredsPrefix = false): string
    {
        $triplet = str_pad($triplet, 3, '0', STR_PAD_LEFT);
        $h = intval($triplet[0]);
        $t = intval($triplet[1]);
        $u = intval($triplet[2]);

        $res = '';

        if ($h > 0 || $needHundredsPrefix) {
            $res .= $digits[$h] . ' trăm ';
        }

        if ($t > 1) {
            $res .= $digits[$t] . ' mươi ';
            if ($u === 1) {
                $res .= 'mốt ';
            } elseif ($u === 5) {
                $res .= 'lăm ';
            } elseif ($u > 0) {
                $res .= $digits[$u] . ' ';
            }
        } elseif ($t === 1) {
            $res .= 'mười ';
            if ($u === 1) {
                $res .= 'một ';
            } elseif ($u === 5) {
                $res .= 'lăm ';
            } elseif ($u > 0) {
                $res .= $digits[$u] . ' ';
            }
        } elseif ($t === 0) {
            if ($h > 0 || $needHundredsPrefix) {
                if ($u > 0) {
                    $res .= 'lẻ ' . $digits[$u] . ' ';
                }
            } else {
                if ($u > 0) {
                    $res .= $digits[$u] . ' ';
                }
            }
        }

        return trim($res);
    }

    /**
     * Sinh đường dẫn ảnh mã VietQR chuẩn Napas247 động.
     */
    public static function generateVietQRUrl(?string $bankCode, ?string $accountNumber, ?string $accountHolder, float $amount = 0, string $memo = ''): ?string
    {
        if (empty($bankCode) || empty($accountNumber)) {
            return null;
        }

        // Chuẩn hóa mã ngân hàng (nếu lưu dạng tên, map sang mã Napas)
        $cleanBank = self::normalizeBankCode($bankCode);
        $cleanAcc = preg_replace('/[^0-9a-zA-Z]/', '', $accountNumber);
        $template = 'compact2'; // Mẫu gọn đẹp có thông tin

        $url = "https://img.vietqr.io/image/{$cleanBank}-{$cleanAcc}-{$template}.png";
        
        $params = [];
        if ($amount > 0) {
            $params['amount'] = (int) $amount;
        }
        if (!empty($memo)) {
            $params['addInfo'] = $memo;
        }
        if (!empty($accountHolder)) {
            $params['accountName'] = $accountHolder;
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Map tên ngân hàng phổ biến sang mã VietQR BIN/Symbol
     */
    public static function normalizeBankCode(string $bank): string
    {
        $bank = mb_strtoupper(trim($bank), 'UTF-8');
        $map = [
            'VIETCOMBANK' => 'VCB',
            'TECHCOMBANK' => 'TCB',
            'MBBANK' => 'MB',
            'MB BANK' => 'MB',
            'VIETINBANK' => 'CTG',
            'BIDV' => 'BIDV',
            'VPBANK' => 'VPB',
            'ACB' => 'ACB',
            'TPBANK' => 'TPB',
            'TP BANK' => 'TPB',
            'SACOMBANK' => 'STB',
            'HDBANK' => 'HDB',
            'VIB' => 'VIB',
            'SHB' => 'SHB',
            'MSB' => 'MSB',
            'SEABANK' => 'SEAB',
            'OCB' => 'OCB',
            'LPBANK' => 'LPB',
            'LIENVIETPOSTBANK' => 'LPB',
            'AGRIBANK' => 'VBA',
        ];

        foreach ($map as $key => $code) {
            if (str_contains($bank, $key)) {
                return $code;
            }
        }

        // Mặc định nếu không map được thì lấy chuỗi chữ cái viết liền
        return preg_replace('/[^A-Z0-9]/', '', $bank) ?: 'ICB';
    }
}
