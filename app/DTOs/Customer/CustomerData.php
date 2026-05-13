<?php

namespace App\DTOs\Customer;

use App\Enums\CustomerStatus;

readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public CustomerStatus $status,
        public bool $inviteToPortal = false,
    ) {}
}
