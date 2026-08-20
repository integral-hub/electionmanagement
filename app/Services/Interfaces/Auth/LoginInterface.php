<?php

declare(strict_types=1);

namespace App\Services\Interfaces\Auth;

use App\Models\User;

interface LoginInterface
{
    public function login(array $credentials): User;

    public function resolveUser(array $credentials): User;

    public function logout(): void;
}
