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

        $response = Http::get('https://pokeapi.co/api/v2/pokemon', [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $data = $response->json();

        $paginator = new LengthAwarePaginator(
            $data['results'],
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

        $pokemon = Pokemon::with('abilities')->banned()->get();
        return Inertia::render('BannedList', [
            'pokemons' => PokemonResource::collection($pokemon),
        ]);
    }

    public function toggleBan(Pokemon $pokemon)
    {
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

        $pokemonModel = Pokemon::firstOrCreate(
            ['name' => $pokemon['name']],
            [
                'image_path' => "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$id}.png",
                'cry' => $pokemon['cries']['latest'],
                'if_banned' => 0,
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
