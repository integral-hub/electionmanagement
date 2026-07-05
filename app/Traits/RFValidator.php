<?php

namespace App\Traits;

use App\Models\Election;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

trait RFValidator
{
    public function rf(Election $election): array
    {
        $fields = $election->registrationField?->fields ?? [];

        $rules = [];

        foreach ($fields as $field) {

            $rules[$field['field_name']] = $this->buildRules($field);
        }

        return $rules;
    }
    public function loginRules(Election $election): array
    {
        $fields = $this->normalizeLoginFields($election);

        $rules = [];

        foreach ($fields as $field) {
            $rules[$field] = match ($field) {
                'email'    => ['bail', 'required', 'email:rfc'],
                'password' => ['bail', 'required'],
                default    => ['required', 'string', 'max:100'],
            };
        }

        return $rules;
    }
    private function buildRules(array $field): array
    {
        $rules = [
            ($field['required'] ?? false) ? 'required' : 'nullable',
            ...$this->mapType($field['field_type'] ?? 'text'),
        ];


        if (!empty($field['options']) && in_array($field['field_type'], ['select', 'radio'])) {
            $rules[] = 'in:' . implode(',', $field['options']);
        }

        if (!empty($field['max_input'])) {
            $rules[] = 'max:' . $field['max_input'];
        }  

        if ($field['unique_field'] ?? false) {
             $voter = request()->route('voter');
            $rule = Rule::unique('voter_unique_data', 'value')
                    ->where('field_name', $field['field_name']);
                if ($voter) $rule->ignore($voter->id, 'voter_id');

            $rules[] = $rule;
    
        }

        return $rules;
    }

    private function mapType(string $type): array
    {
        return match ($type) {
            'email' => ['email:rfc'],
            'date' => ['date'],
            'file' => ['file', 'max:5120', 'mimes:pdf,doc,docx,jpeg,png,jpg'],
            'checkbox' => ['array'],
            'textarea' => ['string', 'max:500'],
            default => ['string', 'max:255'],
        };
    }
    public function normalizeLoginFields(Election $election, bool $isLogin = false): Collection|array
    {
        $loginFields = $election->setting?->login_fields ?? [
            'email' => 'Email Address',
            'password' => 'Password',
        ];

        if($isLogin){
            return $loginFields = collect(
                $election->setting?->login_fields ?? [
                    'email' => 'Email Address',
                    'password' => 'Password',
                ]
            )->map(function ($label, $key) {
                return [
                    'key'   => $key,
                    'label' => $label,
                ];
            });
        }

        return array_keys($loginFields);
    }
    
}