<?php

namespace App\Listeners;

use App\Actions\CleaningJobs\UpdateCleaningJobStatusAction;
use App\Enums\CleaningJobStatus;
use App\Events\PropertyCancelled;
use App\Models\CleaningJob;

class CancelPropertyCleaningJobs
{
    public function __construct(private readonly UpdateCleaningJobStatusAction $updateStatus) {}

    public function handle(PropertyCancelled $event): void
    {
        $event->property->cleaningJobs()
            ->whereIn('status', [CleaningJobStatus::Scheduled, CleaningJobStatus::InProgress])
            ->get()
            ->each(function (CleaningJob $cleaningJob): void {
                $this->updateStatus->execute($cleaningJob, CleaningJobStatus::Cancelled);
            });
    }
}
