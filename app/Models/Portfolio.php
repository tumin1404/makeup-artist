<?php

namespace App\Models;

use App\Traits\HasOptimizedMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory, HasOptimizedMedia;

    protected $guarded = [];

    // Liên kết với bảng Danh mục
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
            'file_path' => ['maxWidth' => 1080, 'quality' => 80],
        ];
    }
}