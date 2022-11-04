<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;

abstract class FormValidator implements ILumenFormRequest
{
    public function validate($data): array
    {
        return $this->makeValidator($data)->validate();
    }

    public function makeValidator($data) {
        return Validator::make(
            $data,
            $this->rules()
        );
    }
}
