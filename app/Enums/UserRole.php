<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Customer = 'customer';
    case Cleaner  = 'cleaner';

    public function label(): string
    {
        return match($this) {
            UserRole::Admin    => 'Administrator',
            UserRole::Cleaner  => 'Window Cleaner',
            UserRole::Customer => 'Customer',
        };
    }
    public static function invitable(): array
    {
        return [self::Admin, self::Cleaner];
    }
}


