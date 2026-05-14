<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            PropertyStatus::Active => 'Active',
            PropertyStatus::Paused => 'Paused',
            PropertyStatus::Cancelled => 'Cancelled',
        };
    }
}
