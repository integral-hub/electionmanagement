<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Voter;
use App\Models\Election;
use App\Models\VoterUniqueData;
use App\Notifications\VoterCreated;
use App\Services\Interfaces\VoterInterface;
use Illuminate\Support\Facades\DB;

class VoterService implements VoterInterface
{
    public function create(Election $election, array $data): Voter
    {
        return DB::transaction(function () use ($election, $data) {

            $fields = $election->registrationField?->fields ?? [];
            $input = $data;

            $voterData = [];
            $uniqueRows = [];

            foreach ($fields as $field) {

                $name = $field['field_name'];

                if (!array_key_exists($name, $input)) {
                    continue;
                }

                $value = $input[$name];

                if ($field['unique_field'] ?? false) {
                    // Unique data only
                    $uniqueRows[] = [
                        'voter_id' => null, // filled later
                        'field_name' => $name,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    continue;
                }

                // Non-unique data - voter_data ONLY
                $voterData[$name] = $value;
            }

            // Create voter
            $voter = Voter::query()->create([
                'organization_id' => $election->organization_id,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'] ?? null,
                'voter_data' => $voterData,
            ]);

            // Attach elections
            $voter->elections()->attach($election->id);

            // Save unique fields
            if (!empty($uniqueRows)) {

                $uniqueRows = array_map(function ($row) use ($voter) {
                        $row['voter_id'] = $voter->id;
                        return $row;
                    }, $uniqueRows);

                VoterUniqueData::insert($uniqueRows);
            }
            
            $voter->notify(new VoterCreated($election));

            return $voter->refresh();
        });
    }
}