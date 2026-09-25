<?php

namespace App\Models;

use App\Traits\HasOptimizedMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, HasOptimizedMedia;

    protected $guarded = [];

    // Thêm đoạn này để ép kiểu dữ liệu thời gian
    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Media columns to automatically optimize.
     */
    public function getMediaOptimizationAttributes(): array
    {
        return [
            'thumbnail' => ['maxWidth' => 1200, 'quality' => 80],
        ];
    }
}