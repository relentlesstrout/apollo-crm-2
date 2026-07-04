<?php

namespace Tests\Unit\Actions\CleaningJobs;

use App\Actions\CleaningJobs\GenerateCleaningJobsAction;
use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Property;
use App\Models\PropertyService;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GenerateCleaningJobsActionTest extends TestCase
{
    use RefreshDatabase;

    private GenerateCleaningJobsAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new GenerateCleaningJobsAction;
    }

    public function test_it_generates_a_job_for_a_due_active_schedule(): void
    {
        $property = Property::factory()->create();
        $this->dueSchedule($property);

        $this->action->execute();

        $this->assertDatabaseCount('cleaning_jobs', 1);
        $this->assertDatabaseHas('cleaning_jobs', [
            'property_id' => $property->id,
            'status' => CleaningJobStatus::Scheduled->value,
        ]);
    }

    public function test_it_snapshots_the_property_service_price(): void
    {
        $property = Property::factory()->create();
        $schedule = $this->dueSchedule($property, price: 3500);

        $this->action->execute();

        $this->assertDatabaseHas('cleaning_job_service', [
            'service_id' => $schedule->service_id,
            'price' => 3500,
            'actual_price' => null,
        ]);
    }

    public function test_it_links_the_contributing_schedule(): void
    {
        $property = Property::factory()->create();
        $schedule = $this->dueSchedule($property);

        $job = $this->action->execute()->first();

        $this->assertDatabaseHas('cleaning_job_schedule', [
            'cleaning_job_id' => $job->id,
            'schedule_id' => $schedule->id,
        ]);
    }

    public function test_it_advances_next_due_at_by_the_frequency(): void
    {
        $property = Property::factory()->create();
        $schedule = $this->dueSchedule($property, frequencyWeeks: 4, nextDue: Carbon::today());

        $this->action->execute();

        $this->assertEquals(
            Carbon::today()->addWeeks(4)->toDateString(),
            $schedule->fresh()->next_due_at->toDateString(),
        );
    }

    public function test_it_groups_multiple_due_schedules_into_one_job(): void
    {
        $property = Property::factory()->create();
        $this->dueSchedule($property);
        $this->dueSchedule($property);

        $jobs = $this->action->execute();

        $this->assertCount(1, $jobs);
        $this->assertDatabaseCount('cleaning_jobs', 1);
        $this->assertEquals(2, $jobs->first()->services()->count());
    }

    public function test_it_dedupes_a_repeated_service_across_schedules(): void
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
        Schedule::factory()->for($property)->count(2)->create([
            'service_id' => $service->id,
            'active_at' => now(),
            'next_due_at' => Carbon::today(),
        ]);

        $job = $this->action->execute()->first();

        $this->assertEquals(1, $job->services()->count());
        $this->assertEquals(2, $job->schedules()->count());
    }

    public function test_it_skips_inactive_schedules(): void
    {
        $property = Property::factory()->create();
        $this->dueSchedule($property)->update(['active_at' => null]);

        $this->action->execute();

        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_it_skips_schedules_not_yet_due(): void
    {
        $property = Property::factory()->create();
        $this->dueSchedule($property, nextDue: Carbon::today()->addWeek());

        $this->action->execute();

        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_it_skips_paused_properties(): void
    {
        $property = Property::factory()->paused()->create();
        $this->dueSchedule($property);

        $this->action->execute();

        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_it_skips_cancelled_properties(): void
    {
        $property = Property::factory()->cancelled()->create();
        $this->dueSchedule($property);

        $this->action->execute();

        $this->assertDatabaseCount('cleaning_jobs', 0);
    }

    public function test_it_is_idempotent_on_a_second_run(): void
    {
        $property = Property::factory()->create();
        $this->dueSchedule($property);

        $this->action->execute();
        $this->action->execute();

        $this->assertDatabaseCount('cleaning_jobs', 1);
    }

    public function test_it_returns_the_generated_jobs(): void
    {
        $this->dueSchedule(Property::factory()->create());
        $this->dueSchedule(Property::factory()->create());

        $jobs = $this->action->execute();

        $this->assertCount(2, $jobs);
        $this->assertInstanceOf(CleaningJob::class, $jobs->first());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function dueSchedule(
        Property $property,
        int $frequencyWeeks = 4,
        int $price = 2000,
        ?Carbon $nextDue = null,
    ): Schedule {
        $service = Service::factory()->create();

        PropertyService::factory()->create([
            'property_id' => $property->id,
            'service_id' => $service->id,
            'price' => $price,
            'effective_from' => Carbon::today()->subMonth(),
            'effective_to' => null,
        ]);

        return Schedule::factory()->for($property)->create([
            'service_id' => $service->id,
            'active_at' => now(),
            'frequency_weeks' => $frequencyWeeks,
            'next_due_at' => $nextDue ?? Carbon::today(),
        ]);
    }
}
