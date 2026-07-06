<?php

namespace App\Actions\Properties;

use App\Actions\Customers\RecomputeCustomerStatusAction;
use App\Enums\PropertyStatus;
use App\Events\PropertyCancelled;
use App\Models\Property;

class UpdatePropertyStatusAction
{
    public function __construct(private readonly RecomputeCustomerStatusAction $recomputeStatus) {}

    public function execute(Property $property, PropertyStatus $newStatus): void
    {
        $property->update(['status' => $newStatus]);
        $this->recomputeStatus->execute($property->customer);

        if ($newStatus === PropertyStatus::Cancelled) {
            PropertyCancelled::dispatch($property);
        }
    }
}
