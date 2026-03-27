<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    // Cho phép nạp hàng loạt tất cả các trường
    protected $guarded = [];

    /**
     * Tự động ép kiểu (Casting)
     * Giúp trường 'features' luôn là mảng khi lấy ra và tự chuyển thành JSON khi lưu vào DB.
     */
    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Định nghĩa các hằng số Phân cấp dịch vụ (Level)
     * Việc dùng hằng số giúp bạn dễ dàng quản lý và tránh viết sai chữ "Basic" hay "Premium" ở nhiều nơi.
     */
    const LEVEL_BASIC = 'Basic';
    const LEVEL_PREMIUM = 'Premium';
    const LEVEL_EXTRA = 'Extra';

    public static function getLevels(): array
    {
        return [
            self::LEVEL_BASIC => 'Cơ bản',
            self::LEVEL_PREMIUM => 'Cao cấp',
            self::LEVEL_EXTRA => 'Dịch vụ thêm',
        ];
    }

    /**
     * Liên kết: Một dịch vụ thuộc về một Danh mục
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Local Scope: Chỉ lấy các dịch vụ đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}