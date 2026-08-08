<?php

namespace App\Http\Controllers;

use App\Actions\Pokemons\DeletePokemonAction;
use App\Actions\Pokemons\EditPokemonAction;
use App\Actions\Pokemons\IndexPokemonAction;
use App\Actions\Pokemons\ShowPokemonAction;
use App\Actions\Pokemons\StorePokemonAction;
use App\Actions\Pokemons\ToggleBanPokemonAction;
use App\Actions\Pokemons\UpdatePokemonAction;
use App\DataTransferObjects\Pokemons\FilterPokemonData;
use App\DataTransferObjects\Pokemons\PokemonData;
use App\DataTransferObjects\Pokemons\PokemonFilesData;
use App\Enums\PokemonType;
use App\Http\Requests\PokemonRequest;
use App\Http\Resources\PokemonResource;
use App\Models\Pokemon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PokemonController extends Controller
{
    public function index(Request $request, IndexPokemonAction $action): Response
    {
        $pokemons = $action->execute(FilterPokemonData::fromRequest($request)); // create + pass the request validated array (install laravel data packages) instead of fromRequest

        return Inertia::render('Pokemons/Index', [
            'pokemons' => PokemonResource::collection($pokemons),
            'pokemonTypes' => PokemonType::forFrontend(),
        ]);
    }

    public function banned(Request $request)
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        $pokemon = Pokemon::where('if_banned', 1)->get();

        return Inertia::render('BannedList', [
            'pokemons' => PokemonResource::collection($pokemon), // read how to return dto or collection of dtos
        ]);
    }

    public function toggleBan(Pokemon $pokemon, Request $request, ToggleBanPokemonAction $action): RedirectResponse
    {
        $pokemon = $action->execute($pokemon, $request->user());

        return back()->with(
            'message',
            $pokemon->if_banned ? 'Pokemon hidden successfully!' : 'Pokemon unhidden successfully!'
        );
    }

    public function create()
    {
        return Inertia::render('Pokemons/Create', [
            'pokemonTypes' => PokemonType::forFrontend(),
        ], );
    }

    public function store(
        PokemonRequest $request,
        PokemonData $data,
        StorePokemonAction $action,
    ): RedirectResponse {
        $files = PokemonFilesData::from([
            'image' => $request->file('image'),
            'cry' => $request->file('cry'),
        ]);

        $pokemon = $action->execute($files, $data, $request->user());

        return redirect()->route('pokemons.show', $pokemon->uuid);
    }

    public function show(Pokemon $pokemon, ShowPokemonAction $action): Response
    {
        $pokemon = $action->execute($pokemon);

        return Inertia::render('Pokemons/Show', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
            'pokemonTypes' => PokemonType::forFrontend(),
        ]);
    }

    public function edit(Pokemon $pokemon, EditPokemonAction $action): Response
    {
        $pokemon = $action->execute($pokemon);

        return Inertia::render('Pokemons/Edit', [
            'pokemon' => new PokemonResource($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
            'pokemonTypes' => PokemonType::forFrontend(),
        ]);
    }

    public function update(
        PokemonRequest $request,
        Pokemon $pokemon,
        UpdatePokemonAction $action,
    ): RedirectResponse {
        $data = PokemonData::from($request->validated());
        $files = PokemonFilesData::from([
            'image' => $request->file('image'),
            'cry' => $request->file('cry'),
        ]);

        $pokemon = $action->execute($pokemon, $data, $files, $request->user());

        return redirect()->route('pokemons.show', $pokemon->uuid);
    }

    public function destroy(Pokemon $pokemon, Request $request, DeletePokemonAction $action): RedirectResponse
    {
        $action->execute($pokemon, $request->user());

        return redirect()->route('dashboard')->with('message', 'Pokemon deleted successfully');
    }
}
