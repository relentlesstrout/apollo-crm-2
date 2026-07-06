<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyService;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GenerateCleaningJobsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_command_generates_due_cleaning_jobs(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();
        PropertyService::factory()->create([
            'property_id' => $property->id,
            'service_id' => $service->id,
            'price' => 2000,
            'effective_from' => Carbon::today()->subMonth(),
            'effective_to' => null,
        ]);
        Schedule::factory()->for($property)->create([
            'service_id' => $service->id,
            'active_at' => now(),
            'next_due_at' => Carbon::today(),
        ]);

        $this->artisan('cleaning-jobs:generate')
            ->expectsOutputToContain('Generated 1 cleaning job(s).')
            ->assertSuccessful();

        $this->assertDatabaseCount('cleaning_jobs', 1);
    }

    public function test_the_command_succeeds_when_nothing_is_due(): void
    {
        $this->artisan('cleaning-jobs:generate')
            ->expectsOutputToContain('Generated 0 cleaning job(s).')
            ->assertSuccessful();

        $this->assertDatabaseCount('cleaning_jobs', 0);
    }
}
