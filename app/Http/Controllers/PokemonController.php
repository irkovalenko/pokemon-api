<?php

namespace App\Http\Controllers;

use App\Models\Ability;
use App\Models\Pokemon;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pokemon = Pokemon::with('abilities')
            ->notBanned()
            ->get();
        return view('home', compact('pokemon'));
    }

    public function banned()
    {
        $pokemon = Pokemon::with('abilities')->banned()->get();
        return view('home', compact('pokemon'));
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
        $data = file_get_contents("https://pokeapi.co/api/v2/pokemon/{$id}/");
        $pokemon = json_decode($data);

        // create or find the pokemon
        $pokemonModel = Pokemon::firstOrCreate(
            ['name' => $pokemon->name]
        );

        // save abilities via pivot
        foreach ($pokemon->abilities as $abilityData) {
            $ability = Ability::firstOrCreate(
                ['name' => $abilityData->ability->name]
            );
            $pokemonModel->abilities()->syncWithoutDetaching($ability->id);
        }

        return view('pokemon', compact('pokemon', 'pokemonModel'));
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
