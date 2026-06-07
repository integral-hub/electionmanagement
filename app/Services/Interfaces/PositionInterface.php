<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Position;

interface PositionInterface
{
    public function create(array $data): Position;
    public function update(Position $position, array $data): Position;
    public function delete(Position $position): array|bool;
}