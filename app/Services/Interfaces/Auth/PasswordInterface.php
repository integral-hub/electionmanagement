<?php
declare(strict_types=1);

namespace App\Services\Interfaces\Auth;

use Illuminate\Database\Eloquent\Model;

interface PasswordInterface
{
    public function sendResetLink(array $data, string $broker = 'users'): array;
    public function reset(array $data, string $broker = 'users'): array;
    public function update(Model $user, array $data): array;
}
