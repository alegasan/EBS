<?php

use App\Models\Attendee;
use Inertia\Testing\AssertableInertia as Assert;

test('authenticated users can view the event index page', function () {
    $attendee = Attendee::factory()->create();

    $this->actingAs($attendee)
        ->get(route('events.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Events/Index'),
        );
});
