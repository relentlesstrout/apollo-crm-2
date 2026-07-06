<?php

namespace Tests\Unit\Actions\CleaningJobs;

use App\Actions\CleaningJobs\RescheduleCleaningJobAction;
use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RescheduleCleaningJobActionTest extends TestCase
{
    use RefreshDatabase;

    private RescheduleCleaningJobAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new RescheduleCleaningJobAction;
    }

    public function test_it_moves_a_scheduled_job_to_a_new_date(): void
    {
        $job = CleaningJob::factory()->create(['scheduled_at' => '2026-07-01']);

        $this->action->execute($job, '2026-07-20');

        $this->assertEquals('2026-07-20', $job->fresh()->scheduled_at->toDateString());
        $this->assertEquals(CleaningJobStatus::Scheduled, $job->fresh()->status);
    }

    public function test_it_revives_a_cancelled_job_back_to_scheduled(): void
    {
        $job = CleaningJob::factory()->cancelled()->create(['scheduled_at' => '2026-07-01']);

        $this->action->execute($job, '2026-08-01');

        $job->refresh();
        $this->assertEquals(CleaningJobStatus::Scheduled, $job->status);
        $this->assertEquals('2026-08-01', $job->scheduled_at->toDateString());
    }

    public function test_it_does_not_touch_linked_schedules(): void
    {
        $job = CleaningJob::factory()->create(['scheduled_at' => '2026-07-01']);
        $schedule = Schedule::factory()->create([
            'active_at' => now(),
            'next_due_at' => '2026-07-29',
        ]);
        $job->schedules()->attach($schedule->id);

        $this->action->execute($job, '2026-07-20');

        $this->assertEquals('2026-07-29', $schedule->fresh()->next_due_at->toDateString());
    }
}
