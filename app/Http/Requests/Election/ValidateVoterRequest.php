<?php

namespace App\Http\Requests\Election;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ValidateVoterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            'status' => [
                'required',
                Rule::in([
                    'validated',
                    'rejected',
                    'banned',
                ]),
            ],
        ];
    
    }
}
