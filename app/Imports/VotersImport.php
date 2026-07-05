<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\VoterStatusEnum;
use App\Models\Election;
use App\Models\Voter;
use App\Notifications\VoterCreated;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class VotersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected int $imported = 0;
    protected int $failed = 0;
    protected array $rowErrors = [];
    private VoterEmailVerificationInterface $emailService;

    public function __construct(
        private readonly Election $election,
        private readonly string $batchCode
    ) {
       $this->emailService = app(VoterEmailVerificationInterface::class);
    }

    public function model(array $row): Voter
    {
        return DB::transaction(function () use ($row) {
            $password = Str::password(12);

            $voter = new Voter([
                'organization_id' => global_data('org_id'),
                'election_id' => $this->election->id,
                'email' => strtolower(trim($row['email'])),
                'phone' => empty($row['phone']) ? null : trim($row['phone']),
                'batch_code' => $this->batchCode,
                'password' => $password,
                'voter_data' => [
                    'full_name' => trim($row['full_name']),
                ],
            ]);

            $voter->save();

            $this->election->voters()->attach($voter->id, [
                'status' => VoterStatusEnum::Validated->value,
                'validated_by' => Auth::id(),
                'validated_at' => now(),
            ]);

            $voter->notify(new VoterCreated($this->election, $password));

            $this->emailService->send($this->election, $voter, 'import');

            $this->imported++;

            return $voter;
        });
    }

    public function rules(): array
    {
        return [
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('voters', 'email'),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]{10,14}$/',
                Rule::unique('voters', 'phone')->whereNotNull('phone'),
            ],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'full_name.required' => 'Full name is required.',
            'email.required'     => 'Email is required.',
            'email.email'        => 'The email address is invalid.',
            'email.unique'       => 'The email already exists.',
            'phone.regex'        => 'Phone number must be international format e.g. 2348012345678.',
            'phone.unique'       => 'The phone number already exists.',
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->failed++;

            $this->rowErrors[] = [
                'row' => $failure->row(),
                'message' => implode(', ', $failure->errors()),
            ];
        }
    }

    public function getStats(): array
    {
        return [
            'imported' => $this->imported,
            'failed' => $this->failed,
            'errors' => $this->rowErrors,
            'batchcode' => $this->batchCode,
        ];
    }
}