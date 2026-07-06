<?php

namespace App\Actions\CleaningJobs;

use App\Enums\CleaningJobStatus;
use App\Enums\PropertyStatus;
use App\Models\CleaningJob;
use App\Models\Property;
use App\Models\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenerateCleaningJobsAction
{
    /**
     * Generate one cleaning job per property for every active schedule that is
     * due today or earlier, snapshotting service prices and advancing each
     * contributing schedule's next_due_at by its frequency.
     *
     * @return Collection<int, CleaningJob>
     */
    public function execute(?Carbon $on = null): Collection
    {
        $on = $on?->copy()->startOfDay() ?? Carbon::today();

        $dueSchedules = Schedule::query()
            ->whereNotNull('active_at')
            ->whereDate('next_due_at', '<=', $on)
            ->whereHas('property', function ($query) {
                $query->where('status', PropertyStatus::Active);
            })
            ->with('property')
            ->get();

        return $dueSchedules
            ->groupBy('property_id')
            ->map(fn (Collection $schedules) => $this->generateForProperty($schedules->first()->property, $schedules, $on))
            ->values();
    }

    /**
     * @param  Collection<int, Schedule>  $schedules
     */
    private function generateForProperty(Property $property, Collection $schedules, Carbon $on): CleaningJob
    {
        return DB::transaction(function () use ($property, $schedules, $on): CleaningJob {
            $job = CleaningJob::create([
                'property_id' => $property->id,
                'status' => CleaningJobStatus::Scheduled,
                'scheduled_at' => $on,
            ]);

            $services = [];

            foreach ($schedules as $schedule) {
                $price = $this->effectivePrice($property, $schedule->service_id, $on);

                if ($price !== null) {
                    $services[$schedule->service_id] = ['price' => $price];
                }

                $job->schedules()->attach($schedule->id);

                $schedule->update([
                    'next_due_at' => Carbon::parse($schedule->next_due_at)->addWeeks($schedule->frequency_weeks),
                ]);
            }

            $job->services()->attach($services);

            return $job;
        });
    }

    private function effectivePrice(Property $property, int $serviceId, Carbon $on): ?int
    {
        $propertyService = $property->propertyServices()
            ->where('service_id', $serviceId)
            ->where('effective_from', '<=', $on)
            ->where(function ($query) use ($on) {
                $query->whereNull('effective_to')->orWhereDate('effective_to', '>=', $on);
            })
            ->orderByDesc('effective_from')
            ->first()
            ?? $property->propertyServices()
                ->where('service_id', $serviceId)
                ->orderByDesc('effective_from')
                ->first();

        return $propertyService?->price;
    }
}
