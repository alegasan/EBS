<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['venue_id', 'title', 'description', 'start_date', 'end_date', 'max_attendees', 'status'])]
class Event extends Model
{
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(Attendee::class, 'bookings');
    }
}
