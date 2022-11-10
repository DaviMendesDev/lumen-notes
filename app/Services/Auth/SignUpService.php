<?php

namespace App\Services\Auth;

use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SignUpService
{
    public function insertUserBy($credentials, SignUpRequest $form) {
        $form->validate($credentials);

        $user = new User($credentials);
        $user->password = Hash::make($credentials['password']);
        return $user->save();
    }
}
