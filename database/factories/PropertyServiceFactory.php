<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyService;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyService>
 */
class PropertyServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'service_id' => Service::factory(),
            'price' => fake()->numberBetween(1000, 10000),
            'description' => fake()->boolean(40) ? fake()->sentence() : null,
            'effective_from' => now()->startOfMonth(),
            'effective_to' => null,
        ];
    }
}
