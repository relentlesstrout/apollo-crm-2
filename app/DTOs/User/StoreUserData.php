<?php

namespace App\DTOs\User;

readonly class StoreUserData
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public string $password,
        public string $role,
    ) {}
}
