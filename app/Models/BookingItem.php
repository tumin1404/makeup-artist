<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $fillable = ['booking_id', 'service_name', 'service_date', 'description', 'price', 'quantity'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
