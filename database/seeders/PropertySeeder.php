<?php

namespace Database\Seeders;

use App\Actions\Customers\RecomputeCustomerStatusAction;
use App\Models\Customer;
use App\Models\Property;
use App\Models\PropertyService;
use App\Models\Service;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = Service::all();
        $recomputeStatus = new RecomputeCustomerStatusAction;

        Customer::all()->each(function (Customer $customer) use ($services, $recomputeStatus) {
            Property::factory()
                ->for($customer)
                ->has(PropertyService::factory()
                    ->count(rand(1,2))->recycle($services))
                ->create();
            Property::factory()
                ->for($customer)
                ->paused()
                ->has(PropertyService::factory()
                    ->count(rand(1,2))->recycle($services))
                ->create();
            Property::factory()
                ->for($customer)
                ->cancelled()
                ->has(PropertyService::factory()
                    ->count(rand(1,2))->recycle($services))
                ->create();
            $recomputeStatus->execute($customer);
        });
    }
}
