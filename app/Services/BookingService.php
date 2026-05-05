<?php

namespace App\Services;

use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

            if($requestedSeats <= 0) {
                throw new Exception('Number of seats must be at least 1');
            }

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

        $booking->update(['status' => 'confirmed']);

        return $booking;
    }

    public function cancel(int $id): Booking
    {
        try {
            $booking = $this->bookingRepo->findById($id);

            if ($booking->status === 'cancelled') {
                throw new Exception('Booking is already cancelled');
            }

            return $this->bookingRepo->cancelBooking($id);
        } catch (ModelNotFoundException) {
            throw new Exception('Booking not found');
        }
    }

    public function getAllBookings(int $perPage = 15)
    {
        return $this->bookingRepo->getPaginated($perPage);
    }
}
