<?php

declare(strict_types=1);

namespace App\Enums;

enum CandidateStatusEnum: string
{
     case Active = 'active';
     case Withdrawn = 'withdrawn';
     
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

}
