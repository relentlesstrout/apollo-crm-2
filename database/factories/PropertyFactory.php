<?php

namespace Database\Factories;

use App\Enums\PropertyStatus;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /** @var array<string, list<string>> */
    private const POSTCODES_BY_AREA = [
        'Whickham' => ['NE16 4AA', 'NE16 4AB', 'NE16 4AD', 'NE16 5AA', 'NE16 5RE'],
        'Rowlands Gill' => ['NE39 1AA', 'NE39 1AB', 'NE39 1AD'],
        'High Spen' => ['NE39 2AA', 'NE39 2AB', 'NE39 2AD'],
        'Ryton' => ['NE40 3AA', 'NE40 3AB', 'NE40 4AA', 'NE40 4AB'],
        'Stella' => ['NE21 4AA', 'NE21 4AB'],
    ];

    public function definition(): array
    {
        $area = fake()->randomElement(array_keys(self::POSTCODES_BY_AREA));

        return [
            'customer_id' => Customer::factory(),
            'house' => fake()->buildingNumber(),
            'street' => fake()->streetName(),
            'area' => $area,
            'postcode' => fake()->randomElement(self::POSTCODES_BY_AREA[$area]),
            'latitude' => null,
            'longitude' => null,
            'notes' => fake()->boolean(40) ? fake()->sentence() : null,
            'status' => PropertyStatus::Active,
        ];
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PropertyStatus::Paused,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PropertyStatus::Cancelled,
        ]);
    }
}
