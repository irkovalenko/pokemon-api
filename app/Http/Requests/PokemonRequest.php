<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PokemonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        // store
        if ($this->isMethod('POST')) {
            return $user !== null;
        }

        // update and delete
        $id = $this->route('id');
        $ownsPokemon = $user->pokemons()->where('pokemons.id', $id)->exists();

        return $user->isAdmin() || $ownsPokemon;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'        => [
                'required',
                'string',
                $id ? "unique:pokemons,name,{$id}" : 'unique:pokemons,name',
            ],
            'type'        => 'required|string',
            'abilities'   => 'required|array',
            'abilities.*' => 'string',
            'image_path'  => 'nullable|string',
            'cry'         => 'nullable|string',
        ];
    }
}
