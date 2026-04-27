<?php

use App\Models\Attendee;
use Inertia\Testing\AssertableInertia as Assert;

test('authenticated users can view the booking create page', function () {
    $attendee = Attendee::factory()->create();

    $this->actingAs($attendee)
        ->get(route('bookings.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bookings/Create'),
        );
});
