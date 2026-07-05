<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Voter;
use App\Models\Election;
use App\Models\VoterUniqueData;
use App\Notifications\VoterCreated;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use App\Services\Interfaces\FileUploadInterface;
use App\Services\Interfaces\VoterInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoterService implements VoterInterface
{
    public function __construct(
        private readonly FileUploadInterface $fileService,
        private readonly VoterEmailVerificationInterface $verifyEmailService
    ) {}

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
                
                // Handle FILE fields
                if (($field['field_type'] ?? null) === 'file') {
                    if ($value instanceof UploadedFile) {
                        $value = $this->fileService->upload($value, 'voters-doc');
                    }
                }

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

                // Non-unique data
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

            $this->verifyEmailService->send($election, $voter);

            return $voter->refresh();
        });
    }
    
    public function update(Voter $voter, array $data): Voter
    {
        $fillable = collect($data)->only(['phone'])->toArray();
        if (! empty($data['password'])) {
            $fillable['password'] = $data['password'];
        }
        $voter->update($fillable);
        return $voter->refresh();
    }
    public function editVoter(Election $election, Voter $voter, array $data): Voter
    {
        return DB::transaction(function () use ($election, $voter, $data) {

            $fields = $election->registrationField?->fields ?? [];

            $voterData = $voter->voter_data ?? [];
            $uniqueInput = [];

            foreach ($fields as $field) {

                $name = $field['field_name'];

                if (!array_key_exists($name, $data)) {
                    continue;
                }

                $value = $data[$name];

                if (($field['field_type'] ?? null) === 'file') {
                    continue;
                }

                if ($field['unique_field'] ?? false) {
                    $uniqueInput[$name] = $value;
                    continue;
                }

                $voterData[$name] = $value;
            }

            /**
            * 1. Update voter core fields
            */
            $voter->update([
                'email' => $data['email'] ?? $voter->email,
                'phone' => $data['phone'] ?? $voter->phone,
                'voter_data' => $voterData,
            ]);

            /**
            * 2. Sync unique fields (voter_unique_data)
            */
            if (!empty($uniqueInput)) {

                foreach ($uniqueInput as $fieldName => $value) {

                    // check if row exists
                    $existing = VoterUniqueData::query()
                        ->where('voter_id', $voter->id)
                        ->where('field_name', $fieldName)
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'value' => $value,
                        ]);
                    } else {
                        VoterUniqueData::create([
                            'voter_id' => $voter->id,
                            'field_name' => $fieldName,
                            'value' => $value,
                        ]);
                    }
                }
            }

            return $voter->refresh();
        });
    }
    // Approve / Reject
    public function updateValidationStatus(Election $election, Voter $voter, string $status, bool $isValid): void 
    {
        DB::transaction(function () use ($election, $voter, $status, $isValid) {

            $election->voters()->updateExistingPivot($voter->id, [
                'status'       => $status,
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

            $voter->votes()
                ->where('election_id', $election->id)
                ->update([
                    'is_valid' => $isValid,
                ]);
        });
    }
}