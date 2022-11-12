<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;

class SignUpRequest extends FormValidator
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150|',
            'email' => 'required|email|max:120|unique:users,email',
            'password' => 'required|string|max:120',
        ];
    }
}
