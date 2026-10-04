<?php

namespace App\Actions\Services;

use App\Models\PropertyService;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class RestoreServiceAction
{
    /**
     * Restore an archived service and the property prices that were archived
     * with it. Prices removed on their own before the archive stay removed.
     * Schedules stay switched off, so each one is checked before it runs again.
     *
     * @return int The number of property prices restored.
     */
    public function execute(Service $service): int
    {
        return DB::transaction(function () use ($service): int {
            $prices = PropertyService::onlyTrashed()
                ->where('service_id', $service->id)
                ->where('deleted_at', $service->deleted_at)
                ->restore();

            $service->restore();

            return $prices;
        });
    }
}
