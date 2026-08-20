<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Election;
use App\Models\Voter;
use Illuminate\Validation\ValidationException;

/**
 * Resolves a Voter from a set of credentials using fields
 * the election has configured for login (email/phone/custom unique
 * fields). Shared by App\Http\Middleware\AuthorizeVoterLogin (web,
 * session-based) and App\Http\Controllers\Api\V1\Auth\VoterAuthController
 * (stateless API) so the lookup logic only lives in one place.
 */
trait ResolvesVoterLogin
{
    use RFValidator;

    public function findVoter(Election $election, array $credentials): ?Voter
    {
        $fields = array_filter(
            $this->normalizeLoginFields($election),
            fn ($field) => $field !== 'password'
        );

        $query = Voter::query()->where('organization_id', $election->organization_id);

        foreach ($fields as $field) {
            $value = $credentials[$field] ?? null;

            if (empty($value)) {
                throw ValidationException::withMessages([
                    $field => ucfirst($field).' is required.',
                ]);
            }

            if (in_array($field, ['email', 'phone'], true)) {
                $query->where($field, $value);
            } else {
                $query->whereHas('uniqueData', function ($q) use ($field, $value) {
                    $q->where('field_name', $field)->where('value', $value);
                });
            }
        }

        return $query->first();
    }
}
