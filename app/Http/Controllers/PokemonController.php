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
use App\DataTransferObjects\Pokemons\PokemonOutputData;
use App\Enums\PokemonType;
use App\Http\Requests\PokemonIndexRequest;
use App\Http\Requests\PokemonRequest;
use App\Models\Pokemon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PokemonController extends Controller
{
    public function index(PokemonIndexRequest $request, IndexPokemonAction $action): Response
    {
        $pokemons = $action->execute(FilterPokemonData::from($request->validated()));

        return Inertia::render('Pokemons/Index', [
            'pokemons' => $pokemons->through(fn(Pokemon $pokemon) => PokemonOutputData::fromModel($pokemon)),
            'pokemonTypes' => PokemonType::forFrontend(),
        ]);
    }

    public function banned(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $pokemons = Pokemon::where('if_banned', 1)->get();

        return Inertia::render('BannedList', [
            'pokemons' => PokemonOutputData::collect($pokemons),
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
        ],);
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
            'pokemon' => PokemonOutputData::fromModel($pokemon),
            'canBeDeletedOrUpdated' => $pokemon->canBeDeletedOrUpdated(),
            'pokemonTypes' => PokemonType::forFrontend(),
        ]);
    }

    public function edit(Pokemon $pokemon, EditPokemonAction $action): Response
    {
        $pokemon = $action->execute($pokemon);

        return Inertia::render('Pokemons/Edit', [
            'pokemon' => PokemonOutputData::fromModel($pokemon),
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
