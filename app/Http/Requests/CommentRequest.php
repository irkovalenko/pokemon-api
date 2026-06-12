<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //for update
        if ($this->isMethod('PATCH')) {
            return [
                'content' => ['required', 'max:1000', 'string'],
            ];
        }


        return [
            'pokemon_id' => [
                'required',
                'exists:pokemons,id'

            ],

            'content'        => [
                'required',
                'max:1000',
                'string'
            ],

            'parent_id' => [
                'nullable',
                'exists:comments,id'
            ]
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        dd($validator->errors()->all());
    }
}
