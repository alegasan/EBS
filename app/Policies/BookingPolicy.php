<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'attendee';
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }
}
