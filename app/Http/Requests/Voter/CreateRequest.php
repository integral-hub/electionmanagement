<?php

namespace App\Http\Requests\Voter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\RFValidator;
use Illuminate\Validation\Rules\Password;

class CreateRequest extends FormRequest
{
    use RFValidator;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $baseRules = [
            'email' => ['required', 'email:rfc', Rule::unique('voters', 'email')],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('voters', 'phone')],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ];

        $election = $this->route('election');

        if (! $election) {
            return $baseRules;
        }

        return array_merge(
            $baseRules,
            $this->rf($election)
        );
    }
    
}