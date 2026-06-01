<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('user')?->id;

        return [
            'name'        => [
                'required',
                'max:255',
                'string',
                $id ? "unique:users,name,{$id}" : 'unique:users,name',
            ],
            'email' => [
                'required',
                'email',
                $id ? "unique:users,email,{$id}" : 'unique:users,email',
            ],
        ];
    }
}
