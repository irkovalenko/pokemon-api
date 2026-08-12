<?php

namespace App\DataTransferObjects;

use App\Models\User;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public \App\Role $role,
        public string $createdAt,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $user->role,
            createdAt: $user->created_at->format('d M Y, H:i:s'),
        );
    }
}
