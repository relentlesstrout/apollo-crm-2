<?php

namespace Database\Factories;

use App\Enums\InviteStatus;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email'      => fake()->unique()->safeEmail(),
            'token'      => bin2hex(random_bytes(32)),
            'invited_by' => User::factory(),
            'role'       => fake()->randomElement(['admin', 'cleaner']),
            'status' => fake()->randomElement(array_column(Invitestatus::cases(), 'value')),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ];
    }
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'accepted_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
