<?php

declare(strict_types=1);

namespace App\Enums;

enum ElectionStatusEnum: string
{
     case Draft = 'draft';
     case Running = 'running';
     case Paused = 'paused';
     case Completed = 'completed';
     case Cancelled = 'cancelled';
     case Scheduled = 'scheduled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public static function selected(): array
    {
        return [
            self::Paused->value,
            self::Running->value,
            self::Cancelled->value,
        ];
    }

}
