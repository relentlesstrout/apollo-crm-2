<?php

namespace App\Actions\Schedules;

use App\DTOs\Schedule\ScheduleData;
use App\Models\Schedule;

class UpdateScheduleAction
{
    public function execute(ScheduleData $data, Schedule $schedule): void
    {
        $schedule->update([
            'service_id' => $data->serviceId,
            'frequency_weeks' => $data->frequencyWeeks,
            'next_due_at' => $data->nextDueAt,
            'active_at' => $data->activeAt,
        ]);
    }
}
