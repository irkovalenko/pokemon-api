<?php

namespace App\Http\Controllers;

use App\Http\Requests\PokemonRequest;
use App\Http\Resources\PokemonResource;
use App\Models\Ability;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PokemonController extends Controller
{

    public function index(Request $request)
    {

        $type = $request->input('type');
        $name = $request->input('name');
        $user = $request->input('user');

        $pokemons = Pokemon::query()
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($name, fn($q) => $q->where('name', 'like', "%{$name}%"))
            ->when($user, fn($q) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$user}%")))
            ->where('if_banned', 0)
            ->with('user')
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
        $pokemon->update([ //update to the contrary of whatever is in db
            'if_banned' => ! $pokemon->if_banned
        ]);

        return back();
    }


    public function create()
    {
        return Inertia::render('Pokemons/Create');
    }


    public function store(PokemonRequest $request)
    {
        $validated = $request->validated();

        $imagePath = null;
        $cryPath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/pokemon', 'public');
        }

        if ($request->hasFile('cry')) {
            $cryPath = $request->file('cry')->store('cries', 'public');
        }

        $pokemon = Pokemon::create([
            'id'   => Pokemon::max('id') + 1,
            'name' => toKebabCase($validated['name']),
            'type' => $validated['type'],
            'image_path' => $imagePath,
            'cry' => $cryPath,
            'if_banned' => 0,
        ]);

        $abilities = json_decode($validated['abilities'], true);

        //abilities table
        foreach ($abilities as $abilityName) {
            $ability = Ability::firstOrCreate(['name' => $abilityName]);
            $pokemon->abilities()->syncWithoutDetaching($ability->id);
        }

        // pokemon_user table
        $pokemon->user()->attach($request->user()->id);

        return redirect()->route('pokemons.show', $pokemon->id);
    }


    public function show(string $id)
    {

        $pokemon = Pokemon::with([
            'abilities',
            'user',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user',
            'comments.replies.user'
        ])
            ->where('id', $id)->firstOrFail();
        return Inertia::render('Pokemons/Show', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
        ]);
    }


    public function edit(string $id)
    {
        $pokemon = Pokemon::with('abilities')->where('id', $id)->firstOrFail();
        return Inertia::render('Pokemons/Edit', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
        ]);
    }


    public function update(PokemonRequest $request, string $id)
    {
        $validated = $request->validated();

        $imagePath = null;
        $cryPath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/pokemon', 'public');
        }

        if ($request->hasFile('cry')) {
            $cryPath = $request->file('cry')->store('cries', 'public');
        }

        $pokemon = Pokemon::where('id', $id)->firstOrFail();
        if (!$pokemon->canBeDeletedOrUpdated()) {
            abort(403, 'This pokemon cannot be updated.');
        }
        $pokemon->update([
            'name' => toKebabCase($validated['name']),
            'type' => $validated['type'],
            'image_path' => $imagePath ?? $pokemon->image_path,
            'cry' => $cryPath ?? $pokemon->cry,
        ]);

        $abilities = json_decode($validated['abilities'], true);

        $abilityIds = collect($abilities)->map(function ($abilityName) {
            return Ability::firstOrCreate(['name' => $abilityName])->id;
        });

        $pokemon->abilities()->sync($abilityIds);

        return redirect()->route('pokemons.show', $pokemon->id);
    }


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

        if ($pokemon->image_path) {
            Storage::disk('public')->delete($pokemon->image_path);
        }

        if ($pokemon->cry) {
            Storage::disk('public')->delete($pokemon->cry);
        }


        $pokemon->delete();
        return redirect()->route('dashboard')->with('message', 'Pokemon deleted successfully');
    }
}
