<?php

namespace App\Http\Resources\Users;

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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles->pluck('name'),
            'email_verified_at' => $this->email_verified_at?->format('d M Y'),
            'created_at' => $this->created_at?->format('d M Y'),
            'deleted_at' => $this->deleted_at?->format('d M Y'),
        ];
    }
}
