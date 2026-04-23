<?php

use App\Models\Attendee;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $attendee = Attendee::factory()->create();
    $this->actingAs($attendee);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
