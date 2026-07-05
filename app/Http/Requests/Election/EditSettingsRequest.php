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
            'registration_mode' => ['required', Rule::in(['open', 'closed'])],
            //'voters_verification_requirement' => ['nullable', 'array'],
            'vote_before_validation' => ['nullable', 'boolean'],
           // 'is_started' => ['nullable', 'boolean'],
            'login_fields' => ['nullable', 'array', 'min:2', 'max:3'],
            'voters_require_2fa' => ['nullable', 'boolean'],
            'voters_2fa_type' => ['nullable', Rule::in(['email', 'none'])],
            'voting_start' => ['nullable','date', 'after_or_equal:now', 'required_with:voting_end'],
            'voting_end' => ['nullable', 'date', 'after:voting_start', 'required_with:voting_start'],
        ];
    }
    protected function prepareForValidation(): void
    {
        $fields = $this->input('login_fields', []);

        $parsed = [];

        foreach ($fields as $item) {

            [$value, $label] = array_pad(explode(',', $item, 2), 2, null);

            if ($value) {
                $parsed[$value] = ucwords($label);
            }
        }

        $this->merge([
            'login_fields' => !empty($parsed) ? $parsed : null,
            'voters_require_2fa' => $this->boolean('voters_require_2fa'),
            'vote_before_validation' => $this->boolean('vote_before_validation'),
            'voters_2fa_type' => $this->boolean('voters_require_2fa')
                                        ? $this->input('voters_2fa_type')
                                        : 'none',
        ]);
    }

}