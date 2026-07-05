<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $table = $this->route('election') ? 'voters' : 'users';
        return ['email' => ['required', 'email', "exists:$table,email"]];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'Account does not exist. Please sign up or provide a valid email address.',
        ];
    }
}
