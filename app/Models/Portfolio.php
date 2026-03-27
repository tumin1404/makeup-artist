<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Portfolio extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Liên kết với bảng Danh mục
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            // Thay 'value' bằng tên cột lưu ảnh của bạn (VD: 'image', 'avatar'...)
            $columnName = 'file_path'; 

            // Chỉ gọi API nén khi có ảnh mới được thêm hoặc sửa
            if (($model->wasRecentlyCreated || $model->wasChanged($columnName)) && !empty($model->{$columnName})) {
                
                // Lấy đường dẫn thực tế của file ảnh
                $path = Storage::disk('public')->path($model->{$columnName});

                // Kiểm tra nếu là file ảnh thì mới nén (bỏ qua video, text...)
                if (file_exists($path) && preg_match('/\.(jpg|jpeg|png|webp)$/i', $path)) {
                    try {
                        set_time_limit(120); // Tăng thời gian chờ lên 2 phút
                        \Tinify\setKey(env('TINYPNG_API_KEY'));
                        \Tinify\fromFile($path)->toFile($path); // Nén và ghi đè file gốc
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Lỗi TinyPNG: ' . $e->getMessage());
                    }
                }
            }
        });
    }
}