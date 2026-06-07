<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\RegistrationField;

interface RegistrationFieldInterface
{
    public function create(array $data): RegistrationField;
    public function update(RegistrationField $registrationField, array $data): RegistrationField;
    public function delete(RegistrationField $registrationField): array;
}