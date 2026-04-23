<?php

use App\Models\Attendee;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::resetPasswords());
});

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();

    $attendee = Attendee::factory()->create();

    $this->post(route('password.email'), ['email' => $attendee->email]);

    Notification::assertSentTo($attendee, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $attendee = Attendee::factory()->create();

    $this->post(route('password.email'), ['email' => $attendee->email]);

    Notification::assertSentTo($attendee, ResetPassword::class, function ($notification) {
        $response = $this->get(route('password.reset', $notification->token));

        $response->assertOk();

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $attendee = Attendee::factory()->create();

    $this->post(route('password.email'), ['email' => $attendee->email]);

    Notification::assertSentTo($attendee, ResetPassword::class, function ($notification) use ($attendee) {
        $response = $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $attendee->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('password cannot be reset with invalid token', function () {
    $attendee = Attendee::factory()->create();

    $response = $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $attendee->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors('email');
});
