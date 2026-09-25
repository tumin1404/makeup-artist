<?php

namespace App\Models;

use App\Services\Media\MediaOptimizerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    // Các cột cho phép lưu/cập nhật hàng loạt
    protected $fillable = [
        'group', 
        'key', 
        'value', 
        'type', 
        'description',
        'recommendation',
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
            Cache::forget('site_settings');

            if ($model->type === 'image' && !empty($model->value) && ($model->wasRecentlyCreated || $model->wasChanged('value'))) {
                $optimizer = app(MediaOptimizerService::class);
                $result = $optimizer->optimizeImage($model->value, ['maxWidth' => 1920, 'quality' => 80]);
                if (!empty($result['success']) && !empty($result['path']) && $result['path'] !== $model->value) {
                    $model->value = $result['path'];
                    $model->saveQuietly();
                    Cache::forget('site_settings');
                }
            }
        });

        static::deleted(function () {
            Cache::forget('site_settings');
        });
    }
}