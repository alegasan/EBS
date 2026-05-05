<?php

namespace Tests\Feature\Services;

use App\Models\Attendee;
use App\Models\Event;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(BookingService::class);
});

it('test_book_event_successfully', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    $booking = $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 2,
    ]);

    expect($booking)->toBeInstanceOf(\App\Models\Booking::class)
        ->and($booking->status)->toEqual('pending');
});

it('test_book_event_already_booked', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 2,
    ]);

    expect(fn () => $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 1,
    ]))->toThrow('You have already booked this event');
});


it('test_book_event_with_insufficient_seats', function () {
    $event = Event::factory()->create(['max_attendees' => 5, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 5,
    ]);

    $attendee2 = Attendee::factory()->create();

    expect(fn () => $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee2->id,
        'seats' => 1,
    ]))->toThrow('Not enough seats available');
});


it('test_book_event_not_upcoming', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'cancelled', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    expect(fn () => $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 2,
    ]))->toThrow('Event is not available for booking');
});

it('test_confirm_booking_successfully', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    $booking = $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 2,
    ]);

    $confirmedBooking = $this->service->confirm($booking->id);

    expect($confirmedBooking->status)->toEqual('confirmed');
});

it('test_confirm_booking_invalid_status', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    $booking = $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 2,
    ]);

    $this->service->confirm($booking->id);

    expect(fn () => $this->service->confirm($booking->id))->toThrow('Only pending bookings can be confirmed');
});

it('test_cancel_booking_successfully', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    $booking = $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 2,
    ]);

    $cancelledBooking = $this->service->cancel($booking->id);

    expect($cancelledBooking->status)->toEqual('cancelled');
});

it('test_book_event_with_invalid_seats', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    expect(fn () => $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 0,
    ]))->toThrow('Number of seats must be at least 1');

    expect(fn () => $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => -1,
    ]))->toThrow('Number of seats must be at least 1');
});

it('test_cancel_booking_already_cancelled', function () {
    $event = Event::factory()->create(['max_attendees' => 100, 'status' => 'upcoming', 'start_date' => now()->addDays(10)]);
    $attendee = Attendee::factory()->create();

    $booking = $this->service->book([
        'event_id' => $event->id,
        'attendee_id' => $attendee->id,
        'seats' => 2,
    ]);

    $this->service->cancel($booking->id);

    expect(fn () => $this->service->cancel($booking->id))->toThrow('Booking is already cancelled');
});

it('test_cancel_booking_not_found', function () {
    expect(fn () => $this->service->cancel(999))->toThrow('Booking not found');
});
