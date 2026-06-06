<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Customer::factory()->create([
            'name' => 'Test Customer',
            'phone' => '07700900000',
            'email' => 'customer@apollo.com',
        ]);

        Customer::factory()->count(10)->create();
        Customer::factory()->count(3)->withPortalAccess()->create();
    }
}
