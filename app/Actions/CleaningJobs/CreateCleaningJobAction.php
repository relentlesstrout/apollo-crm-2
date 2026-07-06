<?php

namespace App\Actions\CleaningJobs;

use App\DTOs\CleaningJob\CleaningJobData;
use App\Enums\CleaningJobStatus;
use App\Models\CleaningJob;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class CreateCleaningJobAction
{
    public function execute(Property $property, CleaningJobData $data): CleaningJob
    {
        return DB::transaction(function () use ($property, $data): CleaningJob {
            $job = CleaningJob::create([
                'property_id' => $property->id,
                'status' => CleaningJobStatus::Scheduled,
                'notes' => $data->notes,
                'scheduled_at' => $data->scheduledAt,
            ]);

            $job->services()->attach(
                collect($data->services)->mapWithKeys(fn (array $service): array => [
                    $service['service_id'] => ['price' => $service['price']],
                ])->all()
            );

            $job->cleaners()->attach($data->cleanerIds);

            return $job;
        });
    }
}
