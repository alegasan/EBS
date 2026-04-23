<?php

use App\Models\Attendee;

test('profile page is displayed', function () {
    $attendee = Attendee::factory()->create();

    $response = $this
        ->actingAs($attendee)
        ->get(route('profile.edit'));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $attendee = Attendee::factory()->create();

    $response = $this
        ->actingAs($attendee)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $attendee->refresh();

    expect($attendee->name)->toBe('Test User');
    expect($attendee->email)->toBe('test@example.com');
    expect($attendee->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $attendee = Attendee::factory()->create();

    $response = $this
        ->actingAs($attendee)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => $attendee->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($attendee->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $attendee = Attendee::factory()->create();

    $response = $this
        ->actingAs($attendee)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($attendee->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $attendee = Attendee::factory()->create();

    $response = $this
        ->actingAs($attendee)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect($attendee->fresh())->not->toBeNull();
});
