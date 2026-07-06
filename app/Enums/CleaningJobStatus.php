<?php

namespace App\Enums;

enum CleaningJobStatus: string
{
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            CleaningJobStatus::Scheduled => 'Scheduled',
            CleaningJobStatus::InProgress => 'In Progress',
            CleaningJobStatus::Completed => 'Completed',
            CleaningJobStatus::Cancelled => 'Cancelled',
        };
    }
}
