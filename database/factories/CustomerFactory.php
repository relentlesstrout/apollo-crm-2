<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->optional(0.7)->unique()->safeEmail(),
            'status' => CustomerStatus::Active,
            'user_id' => null,
        ];
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CustomerStatus::Paused,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CustomerStatus::Cancelled,
        ]);
    }

    public function withPortalAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $attributes['email'] ?? fake()->unique()->safeEmail(),
        ])->afterCreating(function (Customer $customer) {
            $user = User::factory()->create([
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'role' => UserRole::Customer,
            ]);
            $customer->update(['user_id' => $user->id]);
        });
    }
}
