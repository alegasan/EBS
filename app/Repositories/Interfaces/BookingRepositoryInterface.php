<?php

namespace App\Repositories\Interfaces;

interface BookingRepositoryInterface
{
    public function getAll();

    public function findById(int $id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function getByEvent(int $eventId);

    public function getByAttendee(int $attendeeId);

    public function isAlreadyBooked(int $eventId, int $attendeeId): bool;

    public function getTotalBookedSeats(int $eventId): int;

    public function cancelBooking(int $bookingId);

    public function confirmBooking(int $bookingId);

    public function getPaginated(int $perPage);
}
