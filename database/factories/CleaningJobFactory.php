<?php

namespace Database\Factories;

use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CleaningJob>
 */
class CleaningJobFactory extends Factory
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
            'status' => CleaningJobStatus::Scheduled,
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'scheduled_at' => now()->addDays(fake()->numberBetween(1, 14)),
            'started_at' => null,
            'completed_at' => null,
            'invoice_id' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => CleaningJobStatus::InProgress,
            'scheduled_at' => now()->subHours(2),
            'started_at' => now()->subHour(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => CleaningJobStatus::Completed,
            'scheduled_at' => now()->subDay(),
            'started_at' => now()->subDay(),
            'completed_at' => now()->subDay()->addHour(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => CleaningJobStatus::Cancelled,
        ]);
    }
}
