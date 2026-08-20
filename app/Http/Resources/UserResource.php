<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'profile_photo' => $this->profile_photo_path,

            'organization' => $this->whenLoaded('organization', function () {
                return [
                    'uuid' => $this->organization->uuid,
                    'name' => $this->organization->name,
                    'slug' => $this->organization->slug,
                    'email' => $this->organization->email,
                    'phone' => $this->organization->phone,
                    'logo' => $this->organization->logo,
                    'website' => $this->organization->website,
                    'package_type' => $this->organization->package_type,
                    'active' => $this->organization->active,
                ];
            }),

            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name')->values();
            }),

            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->pluck('name')->values();
            }),
        ];
    }
}
