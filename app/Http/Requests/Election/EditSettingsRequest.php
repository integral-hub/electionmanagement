<?php

namespace App\Http\Requests\Election;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'registration_mode' => ['required', Rule::in(['open', 'invite', 'closed'])],
            'voters_verification_requirement' => ['nullable', 'array'],
            'vote_before_validation' => ['nullable', 'boolean'],
            'login_fields' => ['nullable', 'array', 'min:1', 'max:5'],
            'voters_require_2fa' => ['nullable', 'boolean'],
            'voters_2fa_type' => ['nullable', Rule::in(['sms', 'email', 'authenticator'])],
            'voting_start' => ['nullable','date'],
            'voting_end' => ['nullable', 'date', 'after:voting_start'],
        ];
    }
}