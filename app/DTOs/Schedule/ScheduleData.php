<?php

namespace App\DTOs\Schedule;

readonly class ScheduleData
{
    public function __construct(
        public int $serviceId,
        public int $frequencyWeeks,
        public ?string $activeAt,
        public string $nextDueAt,
    ) {}
}
