<?php

namespace App\Http\Requests;

interface ILumenFormRequest
{
    public function rules(): array;
    public function authorize(): bool;
}
