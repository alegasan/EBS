<?php


class BookingRepository implements BookingRepositoryInterface
{
    public function getAll()
    {
        return Booking::with(['event', 'attendee'])->latest()->get();
    }

    public function findById(int $id)
    {
        return Booking::with(['event', 'attendee'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Booking::create($data);
    }

    public function update(int $id, array $data)
    {
        $booking = $this->findById($id);
        $booking->update($data);
        return $booking;
    }

    public function delete(int $id)
    {
        return Booking::destroy($id);
    }

   
    public function getByEvent(int $eventId)
    {
        return Booking::with('attendee')
            ->where('event_id', $eventId)
            ->get();
    }

  
    public function getByAttendee(int $attendeeId)
    {
        return Booking::with('event')
            ->where('attendee_id', $attendeeId)
            ->latest()
            ->get();
    }

    public function isAlreadyBooked(int $eventId, int $attendeeId): bool
    {
        return Booking::where('event_id', $eventId)
            ->where('attendee_id', $attendeeId)
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

  
    public function getTotalBookedSeats(int $eventId): int
    {
        return Booking::where('event_id', $eventId)
            ->where('status', '!=', 'cancelled')
            ->sum('seats');
    }

    public function cancelBooking(int $id)
    {
        $booking = $this->findById($id);
        $booking->update(['status' => 'cancelled']);
        return $booking;
    }

  
    public function confirmBooking(int $id)
    {
        $booking = $this->findById($id);
        $booking->update(['status' => 'confirmed']);
        return $booking;
    }
}