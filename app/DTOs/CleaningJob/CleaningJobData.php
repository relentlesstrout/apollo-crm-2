<?php

namespace App\DTOs\CleaningJob;

readonly class CleaningJobData
{
    /**
     * @param  array<int, array{service_id: int, price: int}>  $services
     * @param  array<int, int>  $cleanerIds
     */
    public function __construct(
        public string $scheduledAt,
        public ?string $notes,
        public array $services,
        public array $cleanerIds,
    ) {}
}
