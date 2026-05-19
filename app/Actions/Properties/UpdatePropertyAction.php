<?php

namespace App\Actions\Properties;

use App\Actions\Customers\RecomputeCustomerStatusAction;
use App\DTOs\Property\PropertyData;
use App\Models\Property;

class UpdatePropertyAction
{
    public function __construct(private readonly RecomputeCustomerStatusAction $recomputeStatus) {}

    public function execute(PropertyData $data, Property $property): void
    {
        $property->update([
            'house' => $data->house,
            'street' => $data->street,
            'area' => $data->area,
            'postcode' => $data->postcode,
            'notes' => $data->notes,
        ]);

        $this->recomputeStatus->execute($property->customer);
    }
}
