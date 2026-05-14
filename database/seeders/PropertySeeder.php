<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::all()->each(function (Customer $customer) {
            Property::factory()
                ->count(1)->create(['customer_id' => $customer->id]);
            Property::factory()->count(1)->paused()->create(['customer_id' => $customer->id]);
            Property::factory()->count(1)->cancelled()->create(['customer_id' => $customer->id]);
        });
    }
}
