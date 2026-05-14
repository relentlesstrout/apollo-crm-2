<?php

namespace App\Actions\Customers;

use App\DTOs\Customer\CustomerData;
use App\DTOs\Property\PropertyData;
use App\Enums\CustomerStatus;
use App\Enums\PropertyStatus;
use App\Models\Customer;
use App\Models\Property;

readonly class CreateCustomerAction
{
    public function __construct(
        private GrantCustomerPortalAccessAction $grantPortalAccess,
    ) {}

    public function execute(CustomerData $customerData): Customer
    {
        $customer = Customer::create([
            'name' => $customerData->name,
            'phone' => $customerData->phone,
            'email' => $customerData->email,
            'status' => CustomerStatus::Active,
        ]);

        if ($customerData->inviteToPortal && $customerData->email) {
            $this->grantPortalAccess->execute($customer);
        }

        return $customer;
    }
}
