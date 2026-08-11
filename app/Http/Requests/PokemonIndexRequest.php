<?php

namespace App\Http\Requests;

use App\Enums\PokemonType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PokemonIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::enum(PokemonType::class)],
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
