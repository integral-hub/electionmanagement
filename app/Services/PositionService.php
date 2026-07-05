<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Position;
use App\Services\Interfaces\PositionInterface;

class PositionService implements PositionInterface
{
    public function create(array $data): Position
    {
        return Position::query()->create($data);
    }

    public function update(Position $position, array $data): Position 
    {

        $position->update($data);

        return $position->refresh();
    }

    public function delete(Position $position): array|bool
    {

        $result = $this->canDelete($position);

        if ($result['status']) return $result;

        return (bool) $position->delete();
    }

    private function canDelete(Position $position): array 
    {
        $status = $position->votes()->exists() || $position->candidates()->exists();

        return [
            'status' => $status,
            'message' => $status
                ? 'Position cannot be deleted because it has associated data.' 
                : null,
        ];
    }
}