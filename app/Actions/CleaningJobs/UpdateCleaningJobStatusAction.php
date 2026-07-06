<?php

namespace App\Actions\CleaningJobs;

use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class UpdateCleaningJobStatusAction
{
    public function execute(CleaningJob $cleaningJob, CleaningJobStatus $newStatus): void
    {
        DB::transaction(function () use ($cleaningJob, $newStatus): void {
            $previousStatus = $cleaningJob->status;

            $attributes = ['status' => $newStatus];

            if ($newStatus === CleaningJobStatus::InProgress && $cleaningJob->started_at === null) {
                $attributes['started_at'] = now();
            }

            if ($newStatus === CleaningJobStatus::Completed) {
                $attributes['started_at'] = $cleaningJob->started_at ?? now();
                $attributes['completed_at'] = now();
            }

            $cleaningJob->update($attributes);

            $isNowResolved = in_array($newStatus, [CleaningJobStatus::Completed, CleaningJobStatus::Cancelled], true);

            if ($previousStatus !== $newStatus && $isNowResolved) {
                $this->advanceContributingSchedules($cleaningJob);
            }
        });
    }

    /**
     * Overwrite next_due_at on each active schedule linked to the job so the
     * next visit falls a full frequency after the completion/cancellation date,
     * never after the date it was originally due.
     */
    private function advanceContributingSchedules(CleaningJob $cleaningJob): void
    {
        $cleaningJob->schedules()
            ->whereNotNull('active_at')
            ->get()
            ->each(function (Schedule $schedule): void {
                $schedule->update([
                    'next_due_at' => now()->addWeeks($schedule->frequency_weeks),
                ]);
            });
    }
}
