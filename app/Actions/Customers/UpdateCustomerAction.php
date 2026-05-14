<?php

namespace App\Actions\Customers;

use App\DTOs\Customer\CustomerData;
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


        $customer->update([
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
        ]);
    }
}
