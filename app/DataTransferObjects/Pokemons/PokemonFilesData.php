<?php

namespace App\DataTransferObjects\Pokemons;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class PokemonFilesData extends Data
{
    public function __construct(
        public ?UploadedFile $image,
        public ?UploadedFile $cry,
    ) {}
}
