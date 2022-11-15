<?php

namespace App\Services\Auth;

use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Rfc4122\UuidV4;

class SignUpService
{
    public function insertUserBy($credentials, SignUpRequest $form)
    {
        $form->validate($credentials);

        $user = new User($credentials);
        $user->password = Hash::make($credentials['password']);
        return $user->save();
    }

    public function makeGuest()
    {
        $guestMainKey = UuidV4::fromBytes(random_bytes(16));
        $user = new User();

        $user->name = 'guest:' . $guestMainKey->toString();
        $user->email = $guestMainKey->toString();
        $user->password = Hash::make($guestMainKey->toString());

        $user->save();
        $user->guest()->create();

        return $user;
    }
}
