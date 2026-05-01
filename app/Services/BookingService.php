<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected EventRepositoryInterface $eventRepo,
        protected BookingRepositoryInterface $bookingRepo
    ) {}

    public function book(array $data): Booking
    {
        return DB::transaction(function () use ($data) {

            $event = $this->eventRepo->findByIdForUpdate($data['event_id']);

            if ($event->status !== 'upcoming' || $event->start_date <= now()) {
                throw new Exception('Event is not available for booking');
            }

            if ($this->bookingRepo->isAlreadyBooked($data['event_id'], $data['attendee_id'])) {
                throw new Exception('You have already booked this event');
            }

            $requestedSeats = $data['seats'];

            $bookedSeats = $this->bookingRepo->getTotalBookedSeats($data['event_id']);

            if ($bookedSeats + $requestedSeats > $event->max_attendees) {
                throw new Exception('Not enough seats available');
            }

            return $this->bookingRepo->create([
                'event_id' => $data['event_id'],
                'attendee_id' => $data['attendee_id'],
                'seats' => $requestedSeats,
                'status' => 'pending',
                'booked_at' => now(),
            ]);
        });
    }

    public function confirm(int $id): Booking
    {
        $booking = $this->bookingRepo->findById($id);

        if ($booking->status !== 'pending') {
            throw new Exception('Only pending bookings can be confirmed');
        }

        return $this->bookingRepo->update($id, ['status' => 'confirmed']);
    }

    public function cancel(int $id): Booking
    {
        return $this->bookingRepo->cancelBooking($id);
    }


    public function getAllBookings(int $perPage = 15)
    {
        return $this->bookingRepo->getPaginated($perPage);
    }

}
