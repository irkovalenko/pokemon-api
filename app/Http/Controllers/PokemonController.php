<?php

namespace App\Http\Controllers;

use App\Enums\PokemonType;
use App\Http\Requests\PokemonRequest;
use App\Http\Resources\PokemonResource;
use App\Models\Ability;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

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
            'pokemonTypes' => PokemonType::forFrontend(),
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

        return back()->with(
            'message',
            $pokemon->if_banned ? 'Pokemon hidden successfully!' : 'Pokemon unhidden successfully!'
        );
    }

    public function create()
    {
        return Inertia::render('Pokemons/Create', [
            'pokemonTypes' => PokemonType::forFrontend()
        ],);
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
            'name' => toKebabCase($validated['name']),
            'type' => $validated['type'],
            'image_path' => $imagePath,
            'cry' => $cryPath,
            'if_banned' => 0,
        ]);

        $abilities = $validated['abilities'];

        foreach ($abilities as $ability) {
            if (!empty($ability['uuid'])) {
                // existing ability chosen from dropdown — reuse as is
                $abilityModel = Ability::where('uuid', $ability['uuid'])->firstOrFail();
            } else {
                // brand new ability — record the current user as its creator
                $abilityModel = Ability::firstOrCreate(
                    ['name' => $ability['name']],
                    ['description' => $ability['description'] ?? null]
                );

                if ($abilityModel->wasRecentlyCreated) {
                    $abilityModel->creator()->attach($request->user()->id);
                }
            }

            $pokemon->abilities()->syncWithoutDetaching($abilityModel->uuid);
        }

        // pokemon_user table
        $pokemon->user()->attach($request->user()->id);

        return redirect()->route('pokemons.show', $pokemon->uuid);
    }

    public function show(string $uuid)
    {
        $pokemon = Pokemon::with([
            'abilities.creator',
            'user',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user',
            'comments.replies.user'
        ])
            ->where('uuid', $uuid)->firstOrFail();

        return Inertia::render('Pokemons/Show', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
            'pokemonTypes' => PokemonType::forFrontend(),
        ]);
    }

    public function edit(string $uuid)
    {
        $pokemon = Pokemon::with('abilities.creator')->where('uuid', $uuid)->firstOrFail();

        return Inertia::render('Pokemons/Edit', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
            'pokemonTypes' => PokemonType::forFrontend(),
        ]);
    }

    public function update(PokemonRequest $request, string $uuid)
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

        $pokemon = Pokemon::where('uuid', $uuid)->firstOrFail();
        if (!$pokemon->canBeDeletedOrUpdated()) {
            abort(403, 'This pokemon cannot be updated.');
        }
        $pokemon->update([
            'name' => toKebabCase($validated['name']),
            'type' => $validated['type'],
            'image_path' => $imagePath ?? $pokemon->image_path,
            'cry' => $cryPath ?? $pokemon->cry,
        ]);

        $abilities = $validated['abilities'];
        $abilityUuids = [];

        foreach ($abilities as $ability) {
            if (!empty($ability['uuid'])) {
                $abilityModel = Ability::where('uuid', $ability['uuid'])->firstOrFail();
            } else {
                $abilityModel = Ability::firstOrCreate(
                    ['name' => $ability['name']],
                    ['description' => $ability['description'] ?? null]
                );

                if ($abilityModel->wasRecentlyCreated) {
                    $abilityModel->creator()->attach($request->user()->id);
                }
            }

            $abilityUuids[] = $abilityModel->uuid;
        }
        Log::info('Syncing abilities', ['abilityUuids' => $abilityUuids]);
        $pokemon->abilities()->sync($abilityUuids);

        return redirect()->route('pokemons.show', $pokemon->uuid);
    }

    public function destroy(string $uuid, Request $request)
    {
        $pokemon = Pokemon::where('uuid', $uuid)->firstOrFail();
        $user = $request->user();
        $ownsPokemon = $user->pokemons()->where('pokemons.uuid', $uuid)->exists();

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
