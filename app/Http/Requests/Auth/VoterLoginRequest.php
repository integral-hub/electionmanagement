<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Traits\RFValidator;
use Illuminate\Foundation\Http\FormRequest;

class VoterLoginRequest extends FormRequest
{
    use RFValidator;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $election = $this->route('election');

        return $this->loginRules($election);
    }
}