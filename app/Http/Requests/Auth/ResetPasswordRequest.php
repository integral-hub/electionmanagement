<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $table = $this->route('election') ? 'voters' : 'users';
        return [
            'token'    => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', "exists:$table,email"],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ];
    }
}
