<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Default model definition
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'organization_id' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SYSTEM ADMIN STATE (Platform Owner)
    |--------------------------------------------------------------------------
    */

    public function systemAdmin(): static
    {
        return $this->state(fn () => [
            'name' => config('settings.system_admin.name'),
            'email' => config('settings.system_admin.email'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'organization_id' => null,
        ]);
    }

    // ORGANIZATION Staff / Election Officers
    public function organizationStaff(int $organizationId): static
    {
        return $this->state(fn () => [
            'organization_id' => $organizationId,
            'email_verified_at' => now(),
        ]);
    }
}