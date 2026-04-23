<?php

namespace App\Policies;

use App\Models\Attendee;
use App\Models\Booking;

class BookingPolicy
{
    public function create(Attendee $user): bool
    {
        return $user->role === 'attendee';
    }

    public function update(Attendee $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function delete(Attendee $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }
}
