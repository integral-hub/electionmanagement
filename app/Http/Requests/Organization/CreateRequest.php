<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\User\CreateRequest as UserCreateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateRequest extends UserCreateRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // remove role from user rules
        unset($rules['role']);

        $rules['org_name'] = ['required', 'string', 'max:255'];
        $rules['password'] = ['required', 'string', 'min:8', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()];
        $rules['access_token'] = ['required', 'string', Rule::exists('organization_tokens', 'token')->where('is_used', 0)];

        return $rules;
    }

    /**
     * Normalize payload for action layer
     */
    public function payload(): array
    {

        return [
            'organization' => [
                'name' => $this->org_name,
                'access_token' => $this->access_token
            ],

            'user' => [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ],
        ];
    }
}