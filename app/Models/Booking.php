<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    // Bỏ khóa bảo vệ, cho phép lưu tất cả các cột
    protected $guarded = [];

    // Mối quan hệ: 1 Booking thuộc về 1 Service
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}