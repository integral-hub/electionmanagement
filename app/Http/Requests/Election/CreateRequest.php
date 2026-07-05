<?php

namespace App\Http\Requests\Election;

use App\Enums\ElectionStatusEnum;
use App\Models\Election;
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:300'],
            'status' => ['nullable', Rule::in(ElectionStatusEnum::selected())],
        ];
    }
}