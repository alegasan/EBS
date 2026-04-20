<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Event;
use App\Models\Attendee;

#[Fillable(['event_id', 'attendee_id', 'status', 'seats', 'booked_at'])]
class Booking extends Model
{
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attendee()
    {
        return $this->belongsTo(Attendee::class);
    }
}
