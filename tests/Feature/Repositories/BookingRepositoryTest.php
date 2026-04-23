<?php

use App\Models\Attendee;
use App\Models\Booking;
use App\Models\Event;
use App\Repositories\BookingRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repo = app(BookingRepository::class);
    $this->event = Event::factory()->create();
    $this->attendeeOne = Attendee::factory()->create();
    $this->attendeeTwo = Attendee::factory()->create();
});

function bookingData(int $eventId, int $attendeeId, array $overrides = []): array
{
    return array_merge([
        'event_id' => $eventId,
        'attendee_id' => $attendeeId,
        'status' => 'confirmed',
    ], $overrides);
}

it('can get all bookings', function () {
    $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->create(bookingData($this->event->id, $this->attendeeTwo->id));

    expect($this->repo->getAll())->toHaveCount(2);
});

it('can find booking by id', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $found = $this->repo->findById($booking->id);

    expect($found->id)->toBe($booking->id)
        ->and($found->status)->toBe('confirmed');
});

it('throws exception when booking not found', function () {
    $this->repo->findById(999);
})->throws(ModelNotFoundException::class);

it('can create a booking', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));

    expect($booking->status)->toBe('confirmed');

    $this->assertDatabaseHas('bookings', [
        'event_id' => $this->event->id,
        'attendee_id' => $this->attendeeOne->id,
        'status' => 'confirmed',
    ]);
});

it('can update a booking', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $updated = $this->repo->update($booking->id, ['status' => 'cancelled']);

    expect($updated->status)->toBe('cancelled');

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'cancelled',
    ]);
});

it('can delete a booking', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->delete($booking->id);

    expect(Booking::find($booking->id))->toBeNull();
});

it('can get bookings by event', function () {
    $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->create(bookingData($this->event->id, $this->attendeeTwo->id));

    expect($this->repo->getByEvent($this->event->id))->toHaveCount(2);
});

it('can get bookings by attendee', function () {
    $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->create(bookingData($this->event->id, $this->attendeeTwo->id));

    expect($this->repo->getByAttendee($this->attendeeOne->id))->toHaveCount(2);
});

it('can check if already booked', function () {
    $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));

    expect($this->repo->isAlreadyBooked($this->event->id, $this->attendeeOne->id))->toBeTrue()
        ->and($this->repo->isAlreadyBooked($this->event->id, $this->attendeeTwo->id))->toBeFalse();
});

it('can get total booked seats', function () {
    $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id, ['seats' => 2]));
    $this->repo->create(bookingData($this->event->id, $this->attendeeTwo->id, ['seats' => 3]));

    expect($this->repo->getTotalBookedSeats($this->event->id))->toBe(5);
});

it('can cancel a booking', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $cancelled = $this->repo->cancelBooking($booking->id);

    expect($cancelled->status)->toBe('cancelled');
});

it('throws exception when cancelling already cancelled booking', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->cancelBooking($booking->id);
    $this->repo->cancelBooking($booking->id);
})->throws(Exception::class, 'Booking is already cancelled');

it('does not count cancelled bookings in total seats', function () {
    $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id, ['seats' => 2]));
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeTwo->id, ['seats' => 3]));
    $this->repo->cancelBooking($booking->id);

    expect($this->repo->getTotalBookedSeats($this->event->id))->toBe(2);
});

it('does not consider cancelled bookings as already booked', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->cancelBooking($booking->id);

    expect($this->repo->isAlreadyBooked($this->event->id, $this->attendeeOne->id))->toBeFalse();
});

it('confirms booking', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id, ['status' => 'pending']));

    $updated = $this->repo->update($booking->id, ['status' => 'confirmed']);

    expect($updated->status)->toBe('confirmed');
});

it('cant confirm cancelled booking', function () {
    $booking = $this->repo->create(bookingData($this->event->id, $this->attendeeOne->id));
    $this->repo->cancelBooking($booking->id);
    $this->repo->confirmBooking($booking->id);
})->throws(Exception::class);
