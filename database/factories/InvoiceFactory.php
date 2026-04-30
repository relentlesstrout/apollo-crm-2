<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => UserRole::Customer]),
            'stripe_invoice_id' => 'in_'.fake()->unique()->regexify('[a-zA-Z0-9]{24}'),
            'stripe_payment_intent_id' => null,
            'description' => 'Window Cleaning Service',
            'amount' => 5000,
            'currency' => 'gbp',
            'status' => InvoiceStatus::Sent,
            'paid_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
            'stripe_payment_intent_id' => 'pi_'.fake()->unique()->regexify('[a-zA-Z0-9]{24}'),
        ]);
    }
}
