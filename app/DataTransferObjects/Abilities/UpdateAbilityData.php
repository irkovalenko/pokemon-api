<?php

namespace App\DataTransferObjects\Abilities;

readonly class UpdateAbilityData
{
    public function __construct(
        public string $name,
        public ?string $description,
    ) {}

    public static function fromArray(array $validated): self
    {
        return new self(
            name: $validated['name'],
            description: $validated['description'] ?? null,
        );
    }
}
