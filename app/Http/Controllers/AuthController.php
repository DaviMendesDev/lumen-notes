<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Services\Auth\SignUpService;
use App\Services\Common\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    public function signup(Request $req, SignUpService $signUpService)
    {
        $signUpService->insertUserBy($req->json()->all(), app(SignUpRequest::class));

        return $this->response->success('User saved successfully.', [
            'user' => User::query()->where('email', $req->json('email'))->firstOrFail(),
        ]);
    }

    public function guest(SignUpService $service)
    {
        $guestUser = $service->makeGuest();

        /** @var AuthService $authService */
        $authService = app(AuthService::class);

        // because guest user authenticate from
        $tokens = $authService->signin($guestUser->email, $guestUser->email);

        return $this->response->success('guest user created.', [
            'user' => $guestUser,
            'accessToken' => $tokens['access'],
            'refreshToken' => $tokens['refresh'],
        ]);
    }

    public function refresh(AuthService $authService)
    {
        return $this->response->success('access token refreshed.', [
            'accessToken' => $authService->refresh(
                \request()->json('refreshToken')
            )
        ]);
    }

    public function me()
    {
        return $this->response->success('user\'s data.', Auth::user()->getAttributes());
    }
}
