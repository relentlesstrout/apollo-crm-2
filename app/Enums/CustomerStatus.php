<?php

namespace App\Enums;

enum CustomerStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            CustomerStatus::Active => 'Active',
            CustomerStatus::Paused => 'Paused',
            CustomerStatus::Cancelled => 'Cancelled',
        };
    }
}
