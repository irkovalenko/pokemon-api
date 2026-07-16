<?php

namespace App\DataTransferObjects\Abilities;

readonly class SearchAbilitiesData
{
    public function __construct(
        public string $query = '',
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            query: $request->input('query', ''),
        );
    }
}
