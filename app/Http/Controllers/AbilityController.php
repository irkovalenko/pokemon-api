<?php

namespace App\Http\Controllers;

use App\Actions\Abilities\SearchAbilitiesAction;
use App\Actions\Abilities\UpdateAbilityAction;
use App\DataTransferObjects\Abilities\UpdateAbilityData;
use App\DataTransferObjects\Abilities\SearchAbilitiesData;
use App\Http\Requests\Abilities\UpdateAbilityRequest;
use App\Http\Resources\AbilityResource;
use App\Models\Ability;
use Illuminate\Http\Request;

class AbilityController extends Controller
{
    public function searchAbility(Request $request, SearchAbilitiesAction $action)
    {
        $data = SearchAbilitiesData::fromRequest($request);

        $abilities = $action->execute($data);

        return AbilityResource::collection($abilities);
    }

    public function update(UpdateAbilityRequest $request, Ability $ability, UpdateAbilityAction $action)
    {
        $data = UpdateAbilityData::fromArray($request->validated());

        $updatedAbility = $action->execute($ability, $data, $request->user());

        return new AbilityResource($updatedAbility);
    }
}
