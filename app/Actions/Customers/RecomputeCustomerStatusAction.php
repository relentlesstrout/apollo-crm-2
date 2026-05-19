<?php

namespace App\Actions\Customers;

use App\Enums\CustomerStatus;
use App\Enums\PropertyStatus;
use App\Models\Customer;

class RecomputeCustomerStatusAction
{
    public static function execute(Customer $customer): void
    {
        $properties = $customer->properties;

        $newStatus = match (true) {
            $properties->contains('status', PropertyStatus::Active) => CustomerStatus::Active,
            $properties->contains('status', PropertyStatus::Paused) => CustomerStatus::Paused,
            default => CustomerStatus::Cancelled,
        };

        $customer->update(['status' => $newStatus]);
    }
}
