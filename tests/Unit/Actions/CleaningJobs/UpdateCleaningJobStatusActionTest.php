<?php

namespace Tests\Unit\Actions\CleaningJobs;

use App\Actions\CleaningJobs\UpdateCleaningJobStatusAction;
use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
