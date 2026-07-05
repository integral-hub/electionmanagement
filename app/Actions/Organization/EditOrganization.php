<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Organization;
use App\Services\Interfaces\FileUploadInterface;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class EditOrganization
{
    use AsAction;

    public function __construct(
        private readonly FileUploadInterface $fileService
    ) {}

    public function handle(Organization $organization, array $data): Organization 
    {
                // handle logo 
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {

            $data['logo'] = $this->fileService->upload($data['logo'], 'organization-logo');
        }

        $organization->update($data);

        return $organization->refresh();
    }

}