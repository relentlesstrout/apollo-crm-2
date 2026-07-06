<?php

namespace App\Actions\Schedules;

use App\DTOs\Schedule\ScheduleData;
use App\Models\Property;
use App\Models\Schedule;

class CreateScheduleAction
{
    public function execute(Property $property, ScheduleData $data): Schedule
    {
        return Schedule::create([
            'property_id' => $property->id,
            'service_id' => $data->serviceId,
            'frequency_weeks' => $data->frequencyWeeks,
            'active_at' => $data->activeAt,
            'next_due_at' => $data->nextDueAt,
        ]);
    }
}
