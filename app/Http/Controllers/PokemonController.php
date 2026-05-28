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

        $response = Http::get('https://pokeapi.co/api/v2/pokemon', [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $data = $response->json();

        $additionalInfo = Http::pool(
            fn($pool) =>
            collect($data['results'])->map(
                fn($pokemon) =>
                $pool->get($pokemon['url'])
            )->toArray()
        );

        $pokemons = collect($additionalInfo)->map(fn($res) => [
            'name' => $res->json('name'),
            'type' => $res->json('types')[0]['type']['name'],
            'image_path' => $res->json('sprites.other.official-artwork.front_default'),
        ])->values();

        $mergeDbData = function ($pokemons) {
            $dbPokemons = Pokemon::whereIn('name', $pokemons->pluck('name'))->get()->keyBy('name');
            return $pokemons->map(fn($p) => [
                ...$p,
                'id'        => $dbPokemons->get($p['name'])?->id,
                'if_banned' => $dbPokemons->get($p['name'])?->if_banned ?? 0,
            ])->values();
        };

        $pokemons = $mergeDbData($pokemons);

        if ($type) {
            $pokemons = $pokemons->filter(fn($p) => $p['type'] === $type)->values();
        }

        if ($name) {
            // Fetch all pokemon names (PokeAPI has 1350+ pokemon)
            $allResponse = Http::get('https://pokeapi.co/api/v2/pokemon', [
                'limit' => 10000,
            ]);

            $allNames = collect($allResponse->json()['results'])
                ->pluck('name')
                ->filter(fn($n) => str_contains($n, strtolower($name))) // ✅ partial match
                ->values();

            // Fetch details for matched names in parallel
            $additionalInfo = Http::pool(
                fn($pool) => $allNames->take(15)->map(
                    fn($pokemonName) =>
                    $pool->get("https://pokeapi.co/api/v2/pokemon/{$pokemonName}")
                )->toArray()
            );

            $pokemons = collect($additionalInfo)->map(fn($res) => [
                'name' => $res->json('name'),
                'type' => $res->json('types')[0]['type']['name'],
                'image_path' => $res->json('sprites.other.official-artwork.front_default'),
            ])->values();


            return Inertia::render('Pokemons/Index', [
                'pokemons' => new LengthAwarePaginator(
                    $pokemons,
                    $allNames->count(),
                    $limit,
                    $page,
                    ['path' => request()->url()]
                ),
            ]);
        }

        $paginator = new LengthAwarePaginator(
            $pokemons,
            $data['count'],
            $limit,
            $page,
            ['path' => request()->url()]
        );

        return Inertia::render('Pokemons/Index', [
            'pokemons' => $paginator,
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");
        $pokemon = json_decode($response->body(), true);

        $numericId = $pokemon['id'];

        $pokemonModel = Pokemon::updateOrCreate(
            ['name' => $pokemon['name']],
            [
                'type' => $pokemon['types'][0]['type']['name'],
                'image_path' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$numericId}.png",
                'cry' => $pokemon['cries']['latest']
            ]
        );

        foreach ($pokemon['abilities'] as $abilityData) {
            $ability = Ability::firstOrCreate(
                ['name' => $abilityData['ability']['name']]
            );
            $pokemonModel->abilities()->syncWithoutDetaching($ability->id);
        }

        return Inertia::render('Pokemons/Show', [
            'pokemon' => $pokemonModel->load('abilities'),
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
