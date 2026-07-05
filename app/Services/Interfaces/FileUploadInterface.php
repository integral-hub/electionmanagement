<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use Illuminate\Http\UploadedFile;


interface FileUploadInterface
{
    public function upload(UploadedFile $file, string $folder = 'uploads'): array;
    public function delete(string $publicId, string $type = 'image'): bool;
}