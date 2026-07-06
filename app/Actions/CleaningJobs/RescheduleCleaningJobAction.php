<?php

namespace App\Actions\CleaningJobs;

use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;

class RescheduleCleaningJobAction
{
    /**
     * Move a job to a new date, returning it to the Scheduled state. Used both
     * to shift an upcoming visit and to revive a cancelled one.
     */
    public function execute(CleaningJob $cleaningJob, string $scheduledAt): void
    {
        $cleaningJob->update([
            'scheduled_at' => $scheduledAt,
            'status' => CleaningJobStatus::Scheduled,
        ]);
    }
}
