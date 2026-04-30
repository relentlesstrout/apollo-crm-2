<?php

namespace App\DTOs\User;

use App\Enums\UserRole;

readonly class UserData
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public ?string $password = null,
        public UserRole $role,
    ) {}
}
