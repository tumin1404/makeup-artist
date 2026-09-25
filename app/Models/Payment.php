<?php
namespace App\Models;

use App\Traits\HasOptimizedMedia;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasOptimizedMedia;

    protected $guarded = [];
    protected $casts = ['payment_date' => 'datetime'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Media columns to automatically optimize.
     */
    public function getMediaOptimizationAttributes(): array
    {
        return [
            'proof_image' => ['maxWidth' => 1600, 'quality' => 80],
        ];
    }
}