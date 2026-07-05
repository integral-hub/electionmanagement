<?php
namespace App\Http\Requests\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Please enter your current password.',
            'password.confirmed'        => 'The new passwords do not match.',
        ];
    }
}
