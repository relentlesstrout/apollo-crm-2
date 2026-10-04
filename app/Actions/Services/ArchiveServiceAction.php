<?php

namespace App\Actions\Services;

use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class ArchiveServiceAction
{
    /**
     * Archive a service and everything that would offer it again: soft delete
     * its property prices (stamped with the service's deleted_at so a restore
     * can find them), switch off its schedules, and remove it from open jobs.
     * Open jobs left with no services are cancelled. Completed and cancelled
     * jobs keep their lines as history.
     *
     * @return array{prices: int, schedules: int, jobsUpdated: int, jobsCancelled: int}
     */
    public function execute(Service $service): array
    {
        return DB::transaction(function () use ($service): array {
            $service->delete();

            $prices = $service->propertyServices()
                ->update(['deleted_at' => $service->deleted_at]);

            $schedules = $service->schedules()
                ->whereNotNull('active_at')
                ->update(['active_at' => null]);

            $jobsUpdated = 0;
            $jobsCancelled = 0;

            CleaningJob::query()
                ->whereIn('status', [CleaningJobStatus::Scheduled, CleaningJobStatus::InProgress])
                ->whereHas('services', fn ($query) => $query->whereKey($service->id))
                ->get()
                ->each(function (CleaningJob $cleaningJob) use ($service, &$jobsUpdated, &$jobsCancelled): void {
                    $cleaningJob->services()->detach($service->id);

                    if ($cleaningJob->services()->doesntExist()) {
                        $cleaningJob->update(['status' => CleaningJobStatus::Cancelled]);
                        $jobsCancelled++;
                    } else {
                        $jobsUpdated++;
                    }
                });

            return [
                'prices' => $prices,
                'schedules' => $schedules,
                'jobsUpdated' => $jobsUpdated,
                'jobsCancelled' => $jobsCancelled,
            ];
        });
    }
}
