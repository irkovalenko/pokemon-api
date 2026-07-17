<?php

namespace App\Actions\Pokemons;

use Illuminate\Http\Request;

class HandlePokemonCryAndImageAction
{
    /**
     * @return array{image_path: ?string, cry: ?string}
     */
    public function execute(Request $request): array
    {
        $imagePath = null;
        $cryPath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/pokemon', 'public');
        }

        if ($request->hasFile('cry')) {
            $cryPath = $request->file('cry')->store('cries', 'public');
        }

        return [
            'image_path' => $imagePath,
            'cry' => $cryPath,
        ];
    }
}
