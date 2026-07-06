<?php

namespace App\Actions\CleaningJobs;

use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
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

            if ($previousStatus === $newStatus) {
                return;
            }

            if ($newStatus === CleaningJobStatus::Completed) {
                // Work happened today, so the next visit is a full frequency from now.
                $this->advanceContributingSchedules($cleaningJob, now());
            } elseif ($newStatus === CleaningJobStatus::Cancelled) {
                // Visit was skipped: keep the cadence rolling from the scheduled date,
                // never resetting the clock to today.
                $this->advanceContributingSchedules($cleaningJob, $cleaningJob->scheduled_at);
            }
        });
    }

    /**
     * Overwrite next_due_at on each active schedule linked to the job to a full
     * frequency after the given anchor date.
     */
    private function advanceContributingSchedules(CleaningJob $cleaningJob, Carbon $from): void
    {
        $cleaningJob->schedules()
            ->whereNotNull('active_at')
            ->get()
            ->each(function (Schedule $schedule) use ($from): void {
                $schedule->update([
                    'next_due_at' => $from->copy()->addWeeks($schedule->frequency_weeks),
                ]);
            });
    }
}
