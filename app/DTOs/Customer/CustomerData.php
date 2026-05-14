<?php

namespace App\DTOs\Customer;

readonly class CustomerData
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public bool $inviteToPortal = false,
    ) {}
}
