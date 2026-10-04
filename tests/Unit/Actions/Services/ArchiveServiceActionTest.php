<?php

namespace Tests\Unit\Actions\Services;

use App\Actions\Services\ArchiveServiceAction;
use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\PropertyService;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveServiceActionTest extends TestCase
{
    use RefreshDatabase;

    private ArchiveServiceAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new ArchiveServiceAction;
    }

    public function test_it_soft_deletes_the_service(): void
    {
        $service = Service::factory()->create();

        $this->action->execute($service);

        $this->assertSoftDeleted($service);
    }

    public function test_it_archives_the_property_prices_with_the_service_timestamp(): void
    {
        $service = Service::factory()->create();
        $first = PropertyService::factory()->create(['service_id' => $service->id]);
        $second = PropertyService::factory()->create(['service_id' => $service->id]);

        $result = $this->action->execute($service);

        $this->assertSame(2, $result['prices']);
        $this->assertSoftDeleted($first);
        $this->assertSoftDeleted($second);
        $this->assertTrue($first->fresh()->deleted_at->equalTo($service->fresh()->deleted_at));
    }

    public function test_it_does_not_change_prices_for_other_services(): void
    {
        $service = Service::factory()->create();
        $otherPrice = PropertyService::factory()->create();

        $this->action->execute($service);

        $this->assertNotSoftDeleted($otherPrice);
    }

    public function test_it_switches_off_the_schedules_for_the_service(): void
    {
        $service = Service::factory()->create();
        $schedule = Schedule::factory()->create(['service_id' => $service->id]);
        $otherSchedule = Schedule::factory()->create();

        $result = $this->action->execute($service);

        $this->assertSame(1, $result['schedules']);
        $this->assertNull($schedule->fresh()->active_at);
        $this->assertNotNull($otherSchedule->fresh()->active_at);
    }

    public function test_it_removes_the_service_from_an_open_job_that_has_other_services(): void
    {
        $service = Service::factory()->create();
        $otherService = Service::factory()->create();
        $job = CleaningJob::factory()->create();
        $job->services()->attach([
            $service->id => ['price' => 1500],
            $otherService->id => ['price' => 4000],
        ]);

        $result = $this->action->execute($service);

        $this->assertSame(1, $result['jobsUpdated']);
        $this->assertSame(CleaningJobStatus::Scheduled, $job->fresh()->status);
        $this->assertEquals([$otherService->id], $job->services()->pluck('services.id')->all());
    }

    public function test_it_cancels_an_open_job_left_with_no_services(): void
    {
        $service = Service::factory()->create();
        $scheduled = CleaningJob::factory()->create();
        $inProgress = CleaningJob::factory()->inProgress()->create();
        $scheduled->services()->attach($service->id, ['price' => 1500]);
        $inProgress->services()->attach($service->id, ['price' => 1500]);

        $result = $this->action->execute($service);

        $this->assertSame(2, $result['jobsCancelled']);
        $this->assertSame(CleaningJobStatus::Cancelled, $scheduled->fresh()->status);
        $this->assertSame(CleaningJobStatus::Cancelled, $inProgress->fresh()->status);
    }

    public function test_it_does_not_move_the_schedule_when_it_cancels_a_job(): void
    {
        $service = Service::factory()->create();
        $schedule = Schedule::factory()->create(['service_id' => $service->id]);
        $nextDueAt = $schedule->next_due_at->toDateString();
        $job = CleaningJob::factory()->create();
        $job->services()->attach($service->id, ['price' => 1500]);
        $job->schedules()->attach($schedule->id);

        $this->action->execute($service);

        $this->assertSame($nextDueAt, $schedule->fresh()->next_due_at->toDateString());
    }

    public function test_it_keeps_the_service_on_completed_and_cancelled_jobs(): void
    {
        $service = Service::factory()->create();
        $completed = CleaningJob::factory()->completed()->create();
        $cancelled = CleaningJob::factory()->cancelled()->create();
        $completed->services()->attach($service->id, ['price' => 1500, 'actual_price' => 1500]);
        $cancelled->services()->attach($service->id, ['price' => 1500]);

        $this->action->execute($service);

        $this->assertSame(1, $completed->services()->count());
        $this->assertSame(1, $cancelled->services()->count());
        $this->assertSame(CleaningJobStatus::Completed, $completed->fresh()->status);
    }
}
