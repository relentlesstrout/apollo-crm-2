<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Schedule;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'service_id' => Service::factory(),
            'frequency_weeks' => fake()->randomElement([4, 8, 12, 16]),
            'active_at' => now(),
            'next_due_at' => now()->addWeek(),
        ];
    }


    public function overdue(): static
    {
        return $this->state(fn () => [
            'next_due_at' => now()->subDays(fake()->numberBetween(1, 14)),
        ]);
    }

    public function dueToday(): static
    {
        return $this->state(fn () => [
            'next_due_at' => now(),
        ]);
    }

    public function dueSoon(): static
    {
        return $this->state(fn () => [
            'next_due_at' => now()->addDays(fake()->numberBetween(1, 14)),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'active_at' => null,
        ]);
    }
}
