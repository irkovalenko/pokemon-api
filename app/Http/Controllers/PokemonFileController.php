<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PokemonFileController extends Controller
{
    public function image(Pokemon $pokemon): StreamedResponse
    {
        abort_unless($pokemon->image_path, 404);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        return $disk->response($pokemon->image_path);
    }

    public function cry(Pokemon $pokemon): StreamedResponse
    {
        abort_unless($pokemon->cry, 404);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('private');

        return $disk->response($pokemon->cry);
    }
}
