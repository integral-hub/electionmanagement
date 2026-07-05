<?php

namespace App\Http\Requests\Candidate;

use App\Enums\CandidateStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:255'],
            'manifesto' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(CandidateStatusEnum::values())],
        ];
    }
}