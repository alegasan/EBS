<?php

use App\Models\Venue;
use App\Repositories\EventRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repo = app(EventRepository::class);
    $this->venue = Venue::create([
        'name' => 'Test Venue',
        'location' => 'Manila',
        'capacity' => 500,
    ]);
});

function eventData(int $venueId, array $overrides = []): array
{
    return array_merge([
        'title' => 'Tech Conference',
        'venue_id' => $venueId,
        'description' => 'Test description',
        'start_date' => '2026-05-01 09:00:00',
        'end_date' => '2026-05-01 17:00:00',
        'max_attendees' => 100,
        'status' => 'upcoming',
    ], $overrides);
}

it('can get all events', function () {
    $this->repo->create(eventData($this->venue->id));
    $this->repo->create(eventData($this->venue->id, ['title' => 'Second Event']));

    expect($this->repo->getAll())->toHaveCount(2);
});

it('can find event by id', function () {
    $event = $this->repo->create(eventData($this->venue->id));
    $found = $this->repo->findById($event->id);

    expect($found->id)->toBe($event->id)
        ->and($found->title)->toBe('Tech Conference');
});

it('throws exception when event not found', function () {
    $this->repo->findById(999);
})->throws(ModelNotFoundException::class);

it('can create an event', function () {
    $event = $this->repo->create(eventData($this->venue->id));

    expect($event->title)->toBe('Tech Conference')
        ->and($event->status)->toBe('upcoming');

    $this->assertDatabaseHas('events', [
        'title' => 'Tech Conference',
        'status' => 'upcoming',
    ]);
});

it('can update an event', function () {
    $event = $this->repo->create(eventData($this->venue->id));
    $updated = $this->repo->update($event->id, ['title' => 'Updated Conference']);

    expect($updated->title)->toBe('Updated Conference');
    $this->assertDatabaseHas('events', ['title' => 'Updated Conference']);
});

it('can delete an event', function () {
    $event = $this->repo->create(eventData($this->venue->id));
    $this->repo->delete($event->id);

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
});

it('can get upcoming events', function () {
    $this->repo->create(eventData($this->venue->id, [
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(8),
        'status' => 'upcoming',
    ]));

    $this->repo->create(eventData($this->venue->id, [
        'title' => 'Past Event',
        'start_date' => now()->subDays(5),
        'end_date' => now()->subDays(4),
        'status' => 'completed',
    ]));

    $upcoming = $this->repo->getUpcomingEvents();

    expect($upcoming)->toHaveCount(1)
        ->and($upcoming->first()->title)->toBe('Tech Conference');
});

it('can filter events by date range', function () {
    $this->repo->create(eventData($this->venue->id, [
        'start_date' => '2026-05-10 09:00:00',
        'end_date' => '2026-05-10 17:00:00',
    ]));

    $this->repo->create(eventData($this->venue->id, [
        'title' => 'Outside Event',
        'start_date' => '2026-08-01 09:00:00',
        'end_date' => '2026-08-01 17:00:00',
    ]));

    $events = $this->repo->filterByDateRange('2026-05-01', '2026-06-01');

    expect($events)->toHaveCount(1)
        ->and($events->first()->title)->toBe('Tech Conference');
});

it('can get available events', function () {
    $this->repo->create(eventData($this->venue->id, [
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(5)->addHours(8),
        'max_attendees' => 100,
    ]));

    $available = $this->repo->getAvailable();

    expect($available)->toHaveCount(1)
        ->and((int) $available->first()->booked_seats)->toBe(0);
});

it('can get events by venue', function () {
    $this->repo->create(eventData($this->venue->id));

    $otherVenue = Venue::create([
        'name' => 'Other Venue',
        'location' => 'Cebu',
        'capacity' => 200,
    ]);

    $this->repo->create(eventData($otherVenue->id, ['title' => 'Cebu Event']));

    $events = $this->repo->getByVenue($this->venue->id);

    expect($events)->toHaveCount(1)
        ->and($events->first()->title)->toBe('Tech Conference');
});

it('can search events by title', function () {
    $this->repo->create(eventData($this->venue->id, ['title' => 'Tech Conference']));
    $this->repo->create(eventData($this->venue->id, ['title' => 'Music Festival']));

    $results = $this->repo->searchByTitle('Tech');

    expect($results)->toHaveCount(1)
        ->and($results->first()->title)->toBe('Tech Conference');
});
