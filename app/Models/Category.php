<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Định nghĩa các hằng số phân loại để tránh gõ sai text
     */
    const TYPE_SERVICE = 'service';
    const TYPE_PORTFOLIO = 'portfolio';
    const TYPE_POST = 'post';

    public static function getTypes(): array
    {
        return [
            self::TYPE_SERVICE => 'Dịch vụ',
            self::TYPE_PORTFOLIO => 'Bộ sưu tập',
            self::TYPE_POST => 'Tạp chí',
        ];
    }

    /**
     * Liên kết: Một danh mục chứa nhiều Bài viết (Tạp chí)
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Liên kết: Một danh mục chứa nhiều Dịch vụ
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Local Scope: Lọc danh mục nhanh theo loại
     * Ví dụ dùng ở Controller: Category::ofType('service')->get();
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}