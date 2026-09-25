<?php
namespace App\Models;

use App\Traits\HasOptimizedMedia;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasOptimizedMedia;

    protected $guarded = [];
    protected $casts = ['expense_date' => 'date'];

    /**
     * Media columns to automatically optimize.
     */
    public function getMediaOptimizationAttributes(): array
    {
        return [
            'product_image' => ['maxWidth' => 1200, 'quality' => 80],
            'receipt_image' => ['maxWidth' => 1600, 'quality' => 80],
        ];
    }
}