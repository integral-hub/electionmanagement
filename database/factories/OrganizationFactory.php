<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'uuid' => Str::uuid()->toString(),
            'name' => $name,
            'slug' => $this->generateSlug($name),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'logo' => null,
            'website' => fake()->url(),
            'active' => true,
        ];
    }

    /**
     * Generate slug like:
     * "Tech Solutions" -> "ts-4821"
     */
    private function generateSlug(string $name): string
    {
        // Take first letter of each word
        $letters = collect(explode(' ', $name))
            ->filter()
            ->map(fn ($word) => strtolower(substr($word, 0, 1)))
            ->implode('');

        // Append random number for uniqueness
        $number = random_int(1000, 9999);

        return $letters . '-' . $number;
    }
}