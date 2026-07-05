<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait ValidatePasswordResetToken
{
    public function isAvailable(?string $email = null, ?string $token = null, string $table = 'password_reset_tokens'): bool
    {
        if (! $email || ! $token) {
            return false;
        }

        // Check email exists in reset table
        $record = DB::table($table)
            ->where('email', $email)
            ->first();

        if (! $record) {
            return false;
        }

        return true;
    }
}