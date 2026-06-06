<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'service_id' => Service::factory(),
            'frequency_weeks' => fake()->randomElement([4, 8, 12]),
            'active_at' => now(),
            'next_due_at' => now()->addWeek()->startOfDay(),
        ];
    }
}
