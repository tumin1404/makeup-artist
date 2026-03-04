<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Liên kết: Một dịch vụ thuộc về một Danh mục (VD: Makeup thuộc danh mục Dịch vụ Cưới)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}