<?php

namespace App\Actions\PropertyServices;

use App\DTOs\PropertyService\PropertyServiceData;
use App\Models\Property;
use App\Models\PropertyService;

class CreatePropertyServiceAction
{
    public function execute(Property $property, PropertyServiceData $data): PropertyService
    {
        return PropertyService::create([
            'property_id' => $property->id,
            'service_id' => $data->serviceId,
            'price' => $data->price,
            'description' => $data->description,
            'effective_from' => $data->effectiveFrom,
            'effective_to' => $data->effectiveTo,
        ]);
    }
}
