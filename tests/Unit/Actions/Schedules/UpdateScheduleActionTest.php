<?php

namespace Tests\Unit\Actions\Schedules;

use App\Actions\Schedules\UpdateScheduleAction;
use App\DTOs\Schedule\ScheduleData;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateScheduleActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdateScheduleAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdateScheduleAction;
    }

    public function test_it_updates_a_schedule(): void
    {
        $schedule = Schedule::factory()->create([
            'frequency_weeks' => 1,
            'next_due_at' => '2026-01-01',
        ]);
        $newService = Service::factory()->create();

        $this->action->execute($this->makeScheduleData(serviceId: $newService->id), $schedule);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'service_id' => $newService->id,
            'frequency_weeks' => 4,
            'active_at' => '2026-06-01 10:00:00',
            'next_due_at' => '2026-06-15',
        ]);
    }

    public function test_it_deactivates_a_schedule(): void
    {
        $schedule = Schedule::factory()->create(['active_at' => '2026-01-01 09:00:00']);

        $this->action->execute($this->makeScheduleData(
            serviceId: $schedule->service_id,
            activeAt: null,
        ), $schedule);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'active_at' => null,
        ]);
    }

    public function test_it_reactivates_an_inactive_schedule(): void
    {
        $schedule = Schedule::factory()->create(['active_at' => null]);

        $this->action->execute($this->makeScheduleData(
            serviceId: $schedule->service_id,
            activeAt: '2026-06-19 12:00:00',
        ), $schedule);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'active_at' => '2026-06-19 12:00:00',
        ]);
    }

    public function test_it_advances_the_next_due_date(): void
    {
        $schedule = Schedule::factory()->create(['next_due_at' => '2026-01-01']);

        $this->action->execute($this->makeScheduleData(
            serviceId: $schedule->service_id,
            nextDueAt: '2026-07-01',
        ), $schedule);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'next_due_at' => '2026-07-01',
        ]);
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
