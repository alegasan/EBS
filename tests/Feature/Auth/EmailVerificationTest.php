<?php

use App\Models\Attendee;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::emailVerification());
});

test('email verification screen can be rendered', function () {
    $attendee = Attendee::factory()->unverified()->create();

    $response = $this->actingAs($attendee)->get(route('verification.notice'));

    $response->assertOk();
});

test('email can be verified', function () {
    $attendee = Attendee::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $attendee->id, 'hash' => sha1($attendee->email)],
    );

    $response = $this->actingAs($attendee)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($attendee->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $attendee = Attendee::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $attendee->id, 'hash' => sha1('wrong-email')],
    );

    $this->actingAs($attendee)->get($verificationUrl);

    Event::assertNotDispatched(Verified::class);
    expect($attendee->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('email is not verified with invalid user id', function () {
    $attendee = Attendee::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $attendee->id + 9999, 'hash' => sha1($attendee->email)],
    );

    $this->actingAs($attendee)->get($verificationUrl);

    Event::assertNotDispatched(Verified::class);
    expect($attendee->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verified user is redirected to dashboard from verification prompt', function () {
    $attendee = Attendee::factory()->create();

    Event::fake();

    $response = $this->actingAs($attendee)->get(route('verification.notice'));

    Event::assertNotDispatched(Verified::class);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('already verified user visiting verification link is redirected without firing event again', function () {
    $attendee = Attendee::factory()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $attendee->id, 'hash' => sha1($attendee->email)],
    );

    $this->actingAs($attendee)->get($verificationUrl)
        ->assertRedirect(route('dashboard', absolute: false).'?verified=1');

    Event::assertNotDispatched(Verified::class);
    expect($attendee->fresh()->hasVerifiedEmail())->toBeTrue();
});
