<?php

namespace App\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'content' => ['required', 'max:1000', 'string'],
            'pokemon_id' => ['required', 'exists:pokemons,uuid'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ];
    }
}
