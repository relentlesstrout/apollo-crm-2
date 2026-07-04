<?php

namespace App\Actions\CleaningJobs;

use App\DTOs\CleaningJob\CleaningJobData;
use App\Models\CleaningJob;
use Illuminate\Support\Facades\DB;

class UpdateCleaningJobAction
{
    public function execute(CleaningJobData $data, CleaningJob $cleaningJob): void
    {
        DB::transaction(function () use ($data, $cleaningJob): void {
            $cleaningJob->update([
                'notes' => $data->notes,
                'scheduled_at' => $data->scheduledAt,
            ]);

            $existingActualPrices = $cleaningJob->services()->pluck('actual_price', 'services.id');

            $cleaningJob->services()->sync(
                collect($data->services)->mapWithKeys(fn (array $service): array => [
                    $service['service_id'] => [
                        'price' => $service['price'],
                        'actual_price' => $existingActualPrices[$service['service_id']] ?? null,
                    ],
                ])->all()
            );

            $cleaningJob->cleaners()->sync($data->cleanerIds);
        });
    }
}
