<?php

namespace App\Http\Controllers;

use App\Http\Requests\PokemonRequest;
use App\Http\Resources\PokemonResource;
use App\Models\Ability;
use App\Models\Pokemon;
use App\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PokemonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $type = $request->input('type');
        $name = $request->input('name');

        $pokemons = Pokemon::query()
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($name, fn($q) => $q->where('name', 'like', "%{$name}%"))
            ->where('if_banned', 0)
            ->paginate();

        return Inertia::render('Pokemons/Index', [
            'pokemons' => PokemonResource::collection($pokemons),
        ]);
    }

    public function banned(Request $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403);
        }

        $pokemon = Pokemon::where('if_banned', 1)->get();

        return Inertia::render('BannedList', [
            'pokemons' => PokemonResource::collection($pokemon),
        ]);
    }

    public function toggleBan(Pokemon $pokemon, Request $request)
    {
        if (!$request->user()?->isAdmin()) {
            abort(403);
        }
        $pokemon->update([
            'if_banned' => ! $pokemon->if_banned
        ]);

        return back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Pokemons/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PokemonRequest $request)
    {
        $validated = $request->validated();

        $pokemon = Pokemon::create([
            'id'   => Pokemon::max('id') + 1, //disabled auto-increment
            'name' => toKebabCase($validated['name']),
            'type' => $validated['type'],
            'image_path' => $validated['image_path'] ?? null,
            'cry' => $validated['cry'] ?? null,
            'if_banned' => 0,
        ]);

        //abilities table
        foreach ($validated['abilities'] as $abilityName) {
            $ability = Ability::firstOrCreate(['name' => $abilityName]);
            $pokemon->abilities()->syncWithoutDetaching($ability->id);
        }

        // pokemon_user table
        $pokemon->users()->attach($request->user()->id);

        return redirect()->route('pokemons.show', $pokemon->name);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $name)
    {

        $pokemon = Pokemon::with('abilities')->where('name', $name)->firstOrFail();
        return Inertia::render('Pokemons/Show', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pokemon = Pokemon::with('abilities')->where('id', $id)->firstOrFail();
        return Inertia::render('Pokemons/Edit', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PokemonRequest $request, string $id)
    {
        $validated = $request->validated();

        $pokemon = Pokemon::where('id', $id)->firstOrFail();
        if (!$pokemon->canBeDeletedOrUpdated()) {
            abort(403, 'This pokemon cannot be updated.');
        }
        $pokemon->update([
            'name' => toKebabCase($validated['name']),
            'type' => $validated['type'],
            'image_path' => $validated['image_path'] ?? $pokemon->image_path,
            'cry' => $validated['cry'] ?? $pokemon->cry,
        ]);

        $abilityIds = collect($validated['abilities'])->map(function ($abilityName) {
            return Ability::firstOrCreate(['name' => $abilityName])->id;
        });

        $pokemon->abilities()->sync($abilityIds);

        return redirect()->route('pokemons.show', $pokemon->name);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $pokemon = Pokemon::where('id', $id)->firstOrFail();
        $user = $request->user();
        $ownsPokemon = $user->pokemons()->where('pokemons.id', $id)->exists();

        if (!$user->isAdmin() && !$ownsPokemon) {
            abort(403);
        }

        if (!$pokemon->canBeDeletedOrUpdated()) {
            abort(403, 'This pokemon cannot be deleted.');
        }


        $pokemon->delete();
        return redirect()->route('pokemons-dashboard')->with('message', 'Pokemon deleted successfully');
    }
}
