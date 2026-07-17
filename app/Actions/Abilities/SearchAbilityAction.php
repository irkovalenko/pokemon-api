<?php

namespace App\Actions\Abilities;

use App\DataTransferObjects\Abilities\SearchAbilityData;
use App\Models\Ability;
use Illuminate\Database\Eloquent\Collection;

class SearchAbilityAction
{
    public function execute(SearchAbilityData $data): Collection
    {
        return Ability::query()
            ->with('creator')
            ->when($data->query, fn($q) => $q->where('name', 'like', "%{$data->query}%"))
            ->limit(10)
            ->get();
    }
}
