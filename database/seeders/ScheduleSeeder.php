<?php

namespace Database\Seeders;

use App\Enums\PropertyStatus;
use App\Models\PropertyService;
use Illuminate\Database\Seeder;
use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PropertyService::with(['property', 'service'])->get()->each(function (PropertyService $propertyService) use ($state) {

            $state = fake()->randomElement(['overdue', 'dueToday', 'dueSoon']);

            if ($propertyService->property->status !== PropertyStatus::Cancelled) {
                Schedule::factory()
                    ->{$state}()
                    ->create([
                    'property_id' => $propertyService->property->id,
                    'service_id' => $propertyService->service->id,
                ]);
            }
            else {
                Schedule::factory()
                    ->inactive()
                    ->create([
                        'property_id' => $propertyService->property->id,
                        'service_id' => $propertyService->service->id,
                        ]);
            }
        });
    }
}
