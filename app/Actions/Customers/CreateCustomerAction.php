<?php

namespace App\Actions\Customers;

use App\DTOs\Customer\CustomerData;
use App\Enums\CustomerStatus;
use App\Models\Customer;

class CreateCustomerAction
{
    public function __construct(
        private readonly GrantCustomerPortalAccessAction $grantPortalAccess,
    ) {}

    public function execute(CustomerData $data): Customer
    {
        $customer = Customer::create([
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
            'status' => CustomerStatus::Active,
        ]);

        if ($data->inviteToPortal && $data->email) {
            $this->grantPortalAccess->execute($customer);
        }

        return $customer;
    }
}
