<?php

namespace App\Listeners;

use App\Events\PropertyCancelled;

class DeactivatePropertySchedules
{
    public function handle(PropertyCancelled $event): void
    {
        $event->property->schedules()->update(['active_at' => null]);
    }
}
