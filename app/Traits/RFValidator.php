<?php

namespace App\Traits;

use App\Models\Election;
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

    private function buildRules(array $field): array
    {
        $rules = [
            ($field['required'] ?? false) ? 'required' : 'nullable',
            ...$this->mapType($field['field_type'] ?? 'text'),
        ];

        if (!empty($field['options']) && in_array($field['field_type'], ['select', 'radio'])) {
            $rules[] = 'in:' . implode(',', $field['options']);
        }

        if ($field['unique_field'] ?? false) {
            $rules[] = Rule::unique('voter_unique_data', 'value')
                ->where('field_name', $field['field_name']);
        }

        return $rules;
    }

    private function mapType(string $type): array
    {
        return match ($type) {
            'email' => ['string', 'email'],
            'date' => ['date'],
            'file' => ['file'],
            'checkbox' => ['array'],
            'textarea' => ['string', 'max:500'],
            default => ['string', 'max:255'],
        };
    }
}