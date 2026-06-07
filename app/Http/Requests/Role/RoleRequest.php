<?php

declare(strict_types=1);

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\PermissionEnum;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:55'],
            'guard_name' => ['sometimes', 'string', 'in:web'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::in(PermissionEnum::values()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.*' => 'One or more permissions are invalid.',
        ];
    }

    /**
     * Normalize payload for service layer
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();

        return [
            'name' => $data['name'] ?? null,
            'guard_name' => $data['guard_name'] ?? 'web',
            'permissions' => $data['permissions'] ?? [],
        ];
    }
}