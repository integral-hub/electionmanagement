<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\User\CreateRequest as UserCreateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        $rules['access_token'] = ['required', 'string', Rule::exists('organization_tokens', 'token')->where('is_used', 0)];

        return $rules;
    }

    /**
     * Normalize payload for action layer
     */
    public function payload(): array
    {
        $token = DB::table('organization_tokens')->where('token', $this->access_token)->first();

        return [
            'organization' => [
                'name' => $this->org_name,
                'package_type' => $token?->name,
            ],

            'user' => [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ],
        ];
    }
}