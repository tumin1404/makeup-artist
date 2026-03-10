<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    // Thêm dòng này để cho phép Filament lưu mọi dữ liệu vào bảng
    protected $guarded = []; 
}