<?php

namespace Tests\Unit\Listeners;

use App\Actions\CleaningJobs\UpdateCleaningJobStatusAction;
use App\Actions\Properties\UpdatePropertyStatusAction;
use App\Enums\CleaningJobStatus;
use App\Enums\PropertyStatus;
use App\Events\PropertyCancelled;
use App\Listeners\CancelPropertyCleaningJobs;
use App\Models\CleaningJob;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelPropertyCleaningJobsTest extends TestCase
{
    use RefreshDatabase;

    private CancelPropertyCleaningJobs $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = new CancelPropertyCleaningJobs(new UpdateCleaningJobStatusAction);
    }

    public function test_it_cancels_scheduled_and_in_progress_jobs(): void
    {
        $property = Property::factory()->create();
        $scheduled = CleaningJob::factory()->for($property)->create();
        $inProgress = CleaningJob::factory()->for($property)->inProgress()->create();

        $this->listener->handle(new PropertyCancelled($property));

        $this->assertEquals(CleaningJobStatus::Cancelled, $scheduled->fresh()->status);
        $this->assertEquals(CleaningJobStatus::Cancelled, $inProgress->fresh()->status);
    }

    public function test_it_leaves_completed_and_cancelled_jobs_untouched(): void
    {
        $property = Property::factory()->create();
        $completed = CleaningJob::factory()->for($property)->completed()->create();
        $alreadyCancelled = CleaningJob::factory()->for($property)->cancelled()->create();

        $this->listener->handle(new PropertyCancelled($property));

        $this->assertEquals(CleaningJobStatus::Completed, $completed->fresh()->status);
        $this->assertEquals(CleaningJobStatus::Cancelled, $alreadyCancelled->fresh()->status);
    }

    public function test_it_does_not_affect_jobs_on_other_properties(): void
    {
        $cancelledProperty = Property::factory()->create();
        $otherProperty = Property::factory()->create();
        CleaningJob::factory()->for($cancelledProperty)->create();
        $otherJob = CleaningJob::factory()->for($otherProperty)->create();

        $this->listener->handle(new PropertyCancelled($cancelledProperty));

        $this->assertEquals(CleaningJobStatus::Scheduled, $otherJob->fresh()->status);
    }

    public function test_it_is_a_no_op_when_the_property_has_no_open_jobs(): void
    {
        $property = Property::factory()->create();

        $this->listener->handle(new PropertyCancelled($property));

        $this->assertEquals(0, $property->cleaningJobs()->count());
    }

    public function test_cancelling_a_property_cancels_its_open_jobs_through_the_event(): void
    {
        $property = Property::factory()->create();
        $job = CleaningJob::factory()->for($property)->create();

        app(UpdatePropertyStatusAction::class)->execute($property, PropertyStatus::Cancelled);

        $this->assertEquals(CleaningJobStatus::Cancelled, $job->fresh()->status);
    }
}
