<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Election;

interface PortalChecklistInterface
{
    public function checklist(Election $election): array;
    public function progress(array $checklist): int;
}