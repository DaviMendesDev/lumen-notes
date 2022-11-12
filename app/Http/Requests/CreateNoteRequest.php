<?php

namespace App\Http\Requests;

class CreateNoteRequest extends FormValidator
{

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|between:2,200'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
