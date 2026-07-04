<?php

namespace App\Actions\CleaningJobs;

use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;

class UpdateCleaningJobStatusAction
{
    public function execute(CleaningJob $cleaningJob, CleaningJobStatus $newStatus): void
    {
        $attributes = ['status' => $newStatus];

        if ($newStatus === CleaningJobStatus::InProgress && $cleaningJob->started_at === null) {
            $attributes['started_at'] = now();
        }

        if ($newStatus === CleaningJobStatus::Completed) {
            $attributes['started_at'] = $cleaningJob->started_at ?? now();
            $attributes['completed_at'] = now();
        }

        $cleaningJob->update($attributes);
    }
}
