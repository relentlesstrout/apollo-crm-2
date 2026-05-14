<?php

namespace App\Actions\Properties;

use App\DTOs\Property\PropertyData;
use App\Models\Property;

class UpdatePropertyAction
{
    public function execute(PropertyData $data, Property $property): void
    {
        $property->update([
            'house' => $data->house,
            'street' => $data->street,
            'area' => $data->area,
            'postcode' => $data->postcode,
            'notes' => $data->notes,
        ]);
    }
}
