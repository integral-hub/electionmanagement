<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RegistrationField;
use App\Services\Interfaces\RegistrationFieldInterface;

class RegistrationFieldService implements RegistrationFieldInterface
{
    public function create(array $data): RegistrationField
    {
        return RegistrationField::query()->create($data);
    }

    public function update(RegistrationField $registrationField, array $data): RegistrationField 
    {
        $registrationField->update($data);

        return $registrationField->refresh();
    }

    public function delete(RegistrationField $registrationField): array 
    {

        $result = $this->canDelete($registrationField);
        if ($result['status']) {
            return $result;
        }

        $registrationField->delete();
        return [
            'status' => true,
            'message' => 'Registration form deleted successfully.',
        ];
    }

    private function canDelete(RegistrationField $registrationField): array 
    {

        $isOpen = $registrationField->election?->setting?->registration_mode === 'open';

        return [
            'status' => $isOpen,
            'message' => $isOpen
                ? 'Registration form cannot be deleted while registration is open.'
                : null,
        ];

    }
}