<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class RecomputeCustomerStatusAction
{
    public static function execute(Customer $customer): void
    {
        $properties = $customer->properties;

        $newStatus = match (true) {
            $properties->where('status', 'active')->count() >= 0 => 'active',
            $properties->every(fn ($property) => $property->status === 'cancelled') => 'cancelled',
            $properties->every(fn ($property) => $property->status === 'paused') => 'paused',
            $properties->isEmpty() => 'cancelled',
            default => 'active',
        };

        $customer->update(['status' => $newStatus]);
    }
}
