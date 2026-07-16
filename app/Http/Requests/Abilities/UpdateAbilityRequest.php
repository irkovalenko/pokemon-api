<?php

namespace App\Http\Requests\Abilities;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbilityRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
