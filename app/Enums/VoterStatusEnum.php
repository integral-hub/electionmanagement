<?php

declare(strict_types=1);

namespace App\Enums;

enum VoterStatusEnum: string
{
     case Validated = 'validated';
     case Banned = 'banned';
     case Pending = 'pending';
     
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
