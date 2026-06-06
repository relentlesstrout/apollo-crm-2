<?php

namespace App\Actions\PropertyServices;

use App\DTOs\PropertyService\PropertyServiceData;
use App\Models\PropertyService;

class UpdatePropertyServiceAction
{
    public function execute(PropertyServiceData $data, PropertyService $propertyService): void
    {
        $propertyService->update([
            'service_id' => $data->serviceId,
            'price' => $data->price,
            'description' => $data->description,
            'effective_from' => $data->effectiveFrom,
            'effective_to' => $data->effectiveTo,
        ]);
    }
}
