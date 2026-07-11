<?php

namespace App\Http\Controllers;

use App\Http\Resources\AbilityResource;
use App\Models\Ability;
use Illuminate\Http\Request;

class AbilityController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        $abilities = Ability::query()
            ->with('creators')
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->limit(10)
            ->get();

        return AbilityResource::collection($abilities);
    }

    public function update(Request $request, Ability $ability)
    {
        $ability->loadMissing('creator');

        if (!$ability->canBeEditedBy($request->user())) {
            abort(403, 'You are not allowed to edit this ability.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $ability->update($validated);

        return new AbilityResource($ability->fresh('creator'));
    }
}
