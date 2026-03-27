<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    // Các cột cho phép lưu/cập nhật hàng loạt
    protected $fillable = [
        'group', 
        'key', 
        'value', 
        'type', 
        'description'
    ];

    /**
     * Hàm hỗ trợ lấy nhanh giá trị theo Key
     * Ví dụ: Setting::get('site_name')
     */
    public static function get($key, $default = null)
    {
        return self::where('key', $key)->value('value') ?? $default;
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            // 1. Chỉ chạy khi type là 'image', có giá trị trong cột 'value', và dữ liệu vừa được tạo/sửa
            if ($model->type === 'image' && !empty($model->value) && ($model->wasRecentlyCreated || $model->wasChanged('value'))) {
                
                // 2. Lấy đường dẫn thực tế của file
                $path = storage_path('app/public/' . $model->value); 

                // 3. Kiểm tra file tồn tại và đúng đuôi ảnh
                if (file_exists($path) && preg_match('/\.(jpg|jpeg|png|webp)$/i', $path)) {
                    try {
                        set_time_limit(120); // Tăng thời gian chờ lên 2 phút
                        \Tinify\setKey(env('TINYPNG_API_KEY'));
                        \Tinify\fromFile($path)->toFile($path);
                    } catch (\Exception $e) {
                        // Ghi log lỗi nếu API hết lượt hoặc lỗi mạng (tùy chọn)
                        \Illuminate\Support\Facades\Log::error('TinyPNG Error: ' . $e->getMessage());
                    }
                }
            }
        });
    }
}