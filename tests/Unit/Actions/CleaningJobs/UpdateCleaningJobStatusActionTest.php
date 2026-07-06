<?php

namespace Tests\Unit\Actions\CleaningJobs;

use App\Actions\CleaningJobs\UpdateCleaningJobStatusAction;
use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UpdateCleaningJobStatusActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdateCleaningJobStatusAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = new UpdateCleaningJobStatusAction;
    }

    public function test_starting_a_job_sets_started_at(): void
    {
        $job = CleaningJob::factory()->create(['started_at' => null]);

        $this->action->execute($job, CleaningJobStatus::InProgress);

        $job->refresh();
        $this->assertEquals(CleaningJobStatus::InProgress, $job->status);
        $this->assertNotNull($job->started_at);
    }

    public function test_starting_does_not_overwrite_an_existing_started_at(): void
    {
        $job = CleaningJob::factory()->create(['started_at' => '2026-07-01 09:00:00']);

        $this->action->execute($job, CleaningJobStatus::InProgress);

        $this->assertEquals('2026-07-01 09:00:00', $job->fresh()->started_at->toDateTimeString());
    }

    public function test_completing_a_job_sets_completed_at(): void
    {
        $job = CleaningJob::factory()->inProgress()->create();

        $this->action->execute($job, CleaningJobStatus::Completed);

        $job->refresh();
        $this->assertEquals(CleaningJobStatus::Completed, $job->status);
        $this->assertNotNull($job->completed_at);
    }

    public function test_completing_directly_from_scheduled_backfills_started_at(): void
    {
        $job = CleaningJob::factory()->create(['started_at' => null]);

        $this->action->execute($job, CleaningJobStatus::Completed);

        $this->assertNotNull($job->fresh()->started_at);
    }

    public function test_cancelling_a_job_only_changes_status(): void
    {
        $job = CleaningJob::factory()->create(['started_at' => null, 'completed_at' => null]);

        $this->action->execute($job, CleaningJobStatus::Cancelled);

        $job->refresh();
        $this->assertEquals(CleaningJobStatus::Cancelled, $job->status);
        $this->assertNull($job->started_at);
        $this->assertNull($job->completed_at);
    }

    public function test_completing_a_job_advances_linked_active_schedules_from_today(): void
    {
        $job = CleaningJob::factory()->inProgress()->create();
        $schedule = $this->linkedSchedule($job, frequencyWeeks: 4, nextDueAt: Carbon::today());

        $this->action->execute($job, CleaningJobStatus::Completed);

        $this->assertEquals(
            Carbon::today()->addWeeks(4)->toDateString(),
            $schedule->fresh()->next_due_at->toDateString(),
        );
    }

    public function test_cancelling_a_job_advances_schedules_from_the_scheduled_date_not_today(): void
    {
        $job = CleaningJob::factory()->create(['scheduled_at' => Carbon::parse('2026-07-01')]);
        $schedule = $this->linkedSchedule($job, frequencyWeeks: 2, nextDueAt: Carbon::today());

        $this->action->execute($job, CleaningJobStatus::Cancelled);

        $this->assertEquals(
            Carbon::parse('2026-07-01')->addWeeks(2)->toDateString(),
            $schedule->fresh()->next_due_at->toDateString(),
        );
    }

    public function test_it_overwrites_a_future_provisional_next_due_at(): void
    {
        $job = CleaningJob::factory()->inProgress()->create();
        $schedule = $this->linkedSchedule($job, frequencyWeeks: 4, nextDueAt: Carbon::today()->addWeeks(2));

        $this->action->execute($job, CleaningJobStatus::Completed);

        $this->assertEquals(
            Carbon::today()->addWeeks(4)->toDateString(),
            $schedule->fresh()->next_due_at->toDateString(),
        );
    }

    public function test_it_does_not_advance_inactive_schedules(): void
    {
        $job = CleaningJob::factory()->inProgress()->create();
        $schedule = $this->linkedSchedule($job, active: false, nextDueAt: Carbon::parse('2026-01-01'));

        $this->action->execute($job, CleaningJobStatus::Completed);

        $this->assertEquals('2026-01-01', $schedule->fresh()->next_due_at->toDateString());
    }

    public function test_it_does_not_advance_schedules_not_linked_to_the_job(): void
    {
        $job = CleaningJob::factory()->inProgress()->create();
        $unlinked = Schedule::factory()->create([
            'active_at' => now(),
            'next_due_at' => Carbon::parse('2026-01-01'),
        ]);

        $this->action->execute($job, CleaningJobStatus::Completed);

        $this->assertEquals('2026-01-01', $unlinked->fresh()->next_due_at->toDateString());
    }

    public function test_it_does_not_advance_schedules_when_merely_starting_a_job(): void
    {
        $job = CleaningJob::factory()->create();
        $schedule = $this->linkedSchedule($job, nextDueAt: Carbon::parse('2026-01-01'));

        $this->action->execute($job, CleaningJobStatus::InProgress);

        $this->assertEquals('2026-01-01', $schedule->fresh()->next_due_at->toDateString());
    }

    public function test_completing_a_manual_job_without_schedules_is_a_no_op_on_schedules(): void
    {
        $job = CleaningJob::factory()->inProgress()->create();

        $this->action->execute($job, CleaningJobStatus::Completed);

        $this->assertEquals(CleaningJobStatus::Completed, $job->fresh()->status);
        $this->assertEquals(0, $job->schedules()->count());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function linkedSchedule(
        CleaningJob $job,
        bool $active = true,
        int $frequencyWeeks = 4,
        ?Carbon $nextDueAt = null,
    ): Schedule {
        $schedule = Schedule::factory()->create([
            'active_at' => $active ? now() : null,
            'frequency_weeks' => $frequencyWeeks,
            'next_due_at' => $nextDueAt ?? Carbon::today(),
        ]);

        $job->schedules()->attach($schedule->id);

        return $schedule;
    }
}
