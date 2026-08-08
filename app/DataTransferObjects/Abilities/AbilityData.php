<?php

namespace App\DataTransferObjects\Abilities;

use Spatie\LaravelData\Data;

class AbilityData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public ?string $uuid,
    ) {}
}
