<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UpdateNoteRequest extends FormValidator
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|between:2,200',
            'content' => 'required|string|max:10000',
        ];
    }
}
