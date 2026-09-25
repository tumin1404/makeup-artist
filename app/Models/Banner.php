<?php

namespace App\Models;

use App\Traits\HasOptimizedMedia;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasOptimizedMedia;

    protected $guarded = [];

    /**
     * Media columns to automatically optimize.
     */
    public function getMediaOptimizationAttributes(): array
    {
        return [
            'image_path' => ['maxWidth' => 1920, 'quality' => 80],
            'image_mobile_path' => ['maxWidth' => 1080, 'quality' => 80],
        ];
    }
}
