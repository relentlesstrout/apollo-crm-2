<?php

namespace App\DTOs\User;

readonly class UserData
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public ?string $password = null,
        public string $role,
    ) {}
}
