<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\FileUploadInterface;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class FileUploadService implements FileUploadInterface
{
    public function __construct(
        private readonly Cloudinary $cloudinary
    ) {}

    /**
     * Upload image or document
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): array 
    {
        $resourceType = $this->detectResourceType($file);
        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $path = $file->getPathname();
        $tempPath = null;

        if ($resourceType === 'raw') {
            $tempPath = $path . '.' . $extension;
            copy($path, $tempPath);
            $path = $tempPath;
        }

        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $path,
                [
                    'folder' => $folder,
                    'resource_type' => $resourceType,
                ]
            );
        } finally {
            if ($tempPath) {
                @unlink($tempPath);
            }
        }

        return [
            'public_id' => $result['public_id'],
            'url'       => $result['secure_url'],
            'format'    => $result['format'] ?? $extension,
        ];
    }
    /**
     * Delete file
     */
    public function delete(string $publicId, string $type = 'image'): bool 
    {

        $result = $this->cloudinary->uploadApi()->destroy(
            $publicId,
            ['resource_type' => $type]
        );

        return ($result['result'] ?? '') === 'ok';
    }

    /**
     * Detect resource type
     */
    private function detectResourceType(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        return str_starts_with($mime, 'image/')
            ? 'image'
            : 'raw'; // pdf, doc, xlsx, etc
    }
}