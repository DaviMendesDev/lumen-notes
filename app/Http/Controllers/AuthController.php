<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Services\Common\AuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request, AuthService $authService)
    {
        $r = $request->json();
        $email = $r->get('email');
        $password = $r->get('password');

        return $authService->signin($email, $password);
    }

    public function signup(Request $req, SignUpRequest $form)
    {
        $credentials = $form->validate($req->json()->all());

        $user = new User($credentials);
        $user->password = Hash::make($credentials['password']);
        $user->save();

        return "User save successfully.";
    }
}
