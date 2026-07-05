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
            'name' => ['required', 'string', 'max:55',
                Rule::unique('roles', 'name')
                    ->where(fn ($query) => $query->where('organization_id', global_data('org_id')))
                    ->ignore($this->route('role')),
            ],
            'guard_name' => ['sometimes', 'string', 'in:web'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [
                'string',
                Rule::in(PermissionEnum::values(true)),
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
            'name' => strtolower($data['name']),
            'guard_name' => $data['guard_name'] ?? 'web',
            'permissions' => $data['permissions'] ?? [],
        ];
    }
}