<?php

namespace App\DataTransferObjects\Abilities;

use Illuminate\Http\Request;

readonly class SearchAbilityData
{
    public function __construct(
        public string $query = '',
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            query: $request->input('query', ''),
        );
    }
}
