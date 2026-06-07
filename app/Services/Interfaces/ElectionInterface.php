<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Election;

interface ElectionInterface
{
    public function create(array $data): Election;
    public function update(Election $election, array $data): Election;
    public function delete(Election $election): array;
}