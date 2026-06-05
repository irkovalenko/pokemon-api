<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

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
            ],
            'type'        => 'required|string',
            'abilities'   => 'required|string',
            'cry'   => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !in_array($value->getClientOriginalExtension(), ['ogg', 'mp3'])) {
                    $fail('The cry must be an mp3 or ogg file.');
                }
            }],
            'image' => ['nullable', function ($attribute, $value, $fail) {
                if ($value && !in_array($value->getClientOriginalExtension(), ['png', 'jpg', 'jpeg'])) {
                    $fail('The image must be a png, jpg, or jpeg file.');
                }
            }]
        ];
    }
}
