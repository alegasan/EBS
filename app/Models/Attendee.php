<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'email', 'phone'])]
class Attendee extends Model
{
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
