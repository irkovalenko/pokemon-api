<?php

namespace App\Http\Requests;

use App\Enums\PokemonType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PokemonRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        // store
        if ($this->isMethod('POST') && $this->routeIs('pokemons.store')) {
            return $user !== null;
        }

        // update and delete
        $uuid = $this->route('uuid');
        $ownsPokemon = $user->pokemons()->where('pokemons.uuid', $uuid)->exists();

        return $user->isAdmin() || $ownsPokemon;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('abilities') && is_string($this->abilities)) {
            $decoded = json_decode($this->abilities, true);

            $this->merge([
                'abilities' => is_array($decoded) ? $decoded : [],
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string'],
            'type'        => ['required', Rule::enum(PokemonType::class)],

            'abilities'                => 'required|array',
            'abilities.*.name'         => 'required|string|max:255',
            'abilities.*.description'  => 'nullable|string',
            'abilities.*.uuid'         => 'nullable|string|uuid|exists:abilities,uuid',

            'cry'   => ['nullable', function ($_attribute, $value, $fail) {
                /*    
            $_attribute means intentionally unused
            closure function does function ($value, $fail)
            */
                if ($value && !in_array($value->getClientOriginalExtension(), ['ogg', 'mp3'])) {
                    $fail('The cry must be an mp3 or ogg file.');
                }
            }],
            'image' => ['nullable', function ($_attribute, $value, $fail) {
                if ($value && !in_array($value->getClientOriginalExtension(), ['png', 'jpg', 'jpeg'])) {
                    $fail('The image must be a png, jpg, or jpeg file.');
                }
            }]
        ];
    }
}
