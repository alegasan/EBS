<?php

use App\Models\Attendee;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::emailVerification());
});

test('sends verification notification', function () {
    Notification::fake();

    $attendee = Attendee::factory()->unverified()->create();

    $this->actingAs($attendee)
        ->post(route('verification.send'))
        ->assertRedirect(route('home'));

    Notification::assertSentTo($attendee, VerifyEmail::class);
});

test('does not send verification notification if email is verified', function () {
    Notification::fake();

    $attendee = Attendee::factory()->create();

    $this->actingAs($attendee)
        ->post(route('verification.send'))
        ->assertRedirect(route('dashboard', absolute: false));

    Notification::assertNothingSent();
});
