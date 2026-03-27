<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    protected $guarded = [];
    protected $casts = ['expense_date' => 'date'];

    protected static function booted()
    {
        static::saved(function ($model) {
            // Khai báo mảng chứa tên các cột lưu ảnh của bảng này
            $imageColumns = ['product_image', 'receipt_image']; // <-- Sửa lại tên cột cho đúng thực tế

            foreach ($imageColumns as $column) {
                // Kiểm tra từng cột xem có dữ liệu mới/thay đổi không
                if (($model->wasRecentlyCreated || $model->wasChanged($column)) && !empty($model->{$column})) {
                    
                    $path = Storage::disk('public')->path($model->{$column});

                    if (file_exists($path) && preg_match('/\.(jpg|jpeg|png|webp)$/i', $path)) {
                        try {
                            set_time_limit(120); // Tăng thời gian chờ lên 2 phút
                            \Tinify\setKey(env('TINYPNG_API_KEY'));
                            \Tinify\fromFile($path)->toFile($path);
                        } catch (\Exception $e) {
                            // Bỏ qua nếu lỗi
                        }
                    }
                }
            }
        });
    }
}