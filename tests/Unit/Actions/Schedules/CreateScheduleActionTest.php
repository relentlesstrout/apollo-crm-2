<?php

namespace Tests\Unit\Actions\Schedules;

use App\Actions\Schedules\CreateScheduleAction;
use App\DTOs\Schedule\ScheduleData;
use App\Models\Property;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateScheduleActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateScheduleAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new CreateScheduleAction;
    }

    public function test_it_creates_a_schedule(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();

        $this->action->execute($property, $this->makeScheduleData(serviceId: $service->id));

        $this->assertDatabaseHas('schedules', [
            'property_id' => $property->id,
            'service_id' => $service->id,
            'frequency_weeks' => 4,
            'active_at' => '2026-06-01 10:00:00',
            'next_due_at' => '2026-06-15',
        ]);
    }

    public function test_it_creates_an_inactive_schedule(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();

        $this->action->execute($property, $this->makeScheduleData(
            serviceId: $service->id,
            activeAt: null,
        ));

        $this->assertDatabaseHas('schedules', [
            'property_id' => $property->id,
            'active_at' => null,
        ]);
    }

    public function test_it_returns_the_created_schedule(): void
    {
        $property = Property::factory()->create();
        $service = Service::factory()->create();

        $schedule = $this->action->execute($property, $this->makeScheduleData(serviceId: $service->id));

        $this->assertEquals($property->id, $schedule->property_id);
        $this->assertEquals($service->id, $schedule->service_id);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function makeScheduleData(
        int $serviceId = 1,
        int $frequencyWeeks = 4,
        ?string $activeAt = '2026-06-01 10:00:00',
        string $nextDueAt = '2026-06-15',
    ): ScheduleData {
        return new ScheduleData(
            serviceId: $serviceId,
            frequencyWeeks: $frequencyWeeks,
            activeAt: $activeAt,
            nextDueAt: $nextDueAt,
        );
    }
}
