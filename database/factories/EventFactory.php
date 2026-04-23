<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 day', '+1 month');
        $endDate = (clone $startDate)->modify('+2 hours');

        return [
            'venue_id' => Venue::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'max_attendees' => fake()->numberBetween(10, 300),
            'status' => 'upcoming',
        ];
    }
}
