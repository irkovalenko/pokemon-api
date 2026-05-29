<?php

namespace App\Http\Controllers;

use App\Http\Resources\PokemonResource;
use App\Models\Ability;
use App\Models\Pokemon;
use App\Role;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;

class PokemonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $type = $request->get('type');
        $name = $request->get('name');

        $pokemons = Pokemon::query()
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($name, fn($q) => $q->where('name', 'like', "%{$name}%"))
            ->where('if_banned', 0)
            ->paginate($limit);

        return Inertia::render('Pokemons/Index', [
            'pokemons' => PokemonResource::collection($pokemons),
        ]);
    }

    public function banned(Request $request)
    {
        if ($request->user()?->role !== Role::ADMIN) {
            abort(403);
        }

        $pokemon = Pokemon::where('if_banned', 1)->get();

        return Inertia::render('BannedList', [
            'pokemons' => PokemonResource::collection($pokemon),
        ]);
    }

    public function toggleBan(Pokemon $pokemon, Request $request)
    {
        if ($request->user()?->role !== Role::ADMIN) {
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|unique:pokemons,name',
            'type'       => 'required|string',
            'abilities'  => 'required|array',
            'abilities.*' => 'string',
            'image_path' => 'nullable|string',
            'cry' => 'nullable|string',
        ]);

        $pokemon = Pokemon::create([
            'id'   => Pokemon::max('id') + 1, //disabled auto-increment
            'name' => $validated['name'],
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
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
