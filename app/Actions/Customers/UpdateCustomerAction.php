<?php

namespace App\Actions\Customers;

use App\DTOs\Customer\CustomerData;
use App\Enums\CustomerStatus;
use App\Models\Customer;

class UpdateCustomerAction
{
    public function execute(CustomerData $data, Customer $customer): void
    {
        if ($customer->user_id) {
            $customer->user->update([
                'name' => $data->name,
                'phone' => $data->phone,
                'email' => $data->email,
            ]);
        }

        if ($data->status === CustomerStatus::Cancelled && $customer->status !== CustomerStatus::Cancelled) {
            $customer->user?->delete();
        }

        $customer->update([
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
            'status' => $data->status,
        ]);
    }
}
