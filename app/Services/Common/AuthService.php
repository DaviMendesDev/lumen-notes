<?php

namespace App\Services\Common;

use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Lumen\Http\Request;

class AuthService
{
    /**
     * @param string $email
     * @param string $password
     * @return array
     */
    public function signin(string $email, string $password): array {
        if (! $user = User::where('email', $email)->first()) {
            throw new UnauthorizedException("email/password incorrect");
        }

        if (! Hash::check($password, $user->password)) {
            throw new UnauthorizedException("email/password incorrect");
        }

        $refreshToken = $this->genRefreshToken($user);
        $this->storeRefreshToken($refreshToken['jti'], $refreshToken['jwt']);

        return [
            'access' => $this->genAccessToken($user, $refreshToken['jti']),
            'refresh' => $refreshToken['jwt'],
        ];
    }

    private function genAccessToken(Authenticatable $user, string $refreshTokenId): string
    {
        $payload = [
            'sub' => $user->getAuthIdentifier(),
            'iss' => \request()->header('host'),
            'aud' => config('app.url'),
            'exp' => Carbon::now()->addMinutes(30)->timestamp,
            'iat' => time(),
            'refreshTokenId' => $refreshTokenId,
        ];

        return JWT::encode(
            $payload,
            config('jwt.secret', 'secret'),
            'HS256'
        );
    }

    private function genRefreshToken(Authenticatable $user): array
    {
        $jti = $this->genSecureUUID();
        $payload = [
            'sub' => $user->getAuthIdentifier(),
            'iss' => \request()->header('host'),
            'aud' => config('app.url'),
            'exp' => Carbon::now()->addDays(30)->timestamp,
            'iat' => time(),
            'jti' => $jti,
        ];

        return [
            'jwt' => JWT::encode(
                $payload,
                config('jwt.secret', 'secret'),
                'HS256'
            ),
            'jti' => $jti,
        ];
    }

    private function storeRefreshToken(string $uuid, string $token): static
    {
        Cache::store('redis')->set($uuid, $token, config('jwt.refresh.ttl'));

        return $this;
    }

    private function genSecureUUID(): string
    {
        return \Ramsey\Uuid\Uuid::fromBytes(
            openssl_random_pseudo_bytes(16)
        )->toString();
    }

    public function authenticate(string $accessToken): Authenticatable|null
    {
        $payload = JWT::decode($accessToken, config('jwt.secret', 'secret'));

        if (! $userId = $payload['sub']) {
            throw new UnauthorizedException("Token invalid!");
        }

        if (! $user = User::find($userId)) {
            throw new UnauthorizedException("User is not activated at all.");
        }

        return $user;
    }

    public function refresh(string $refreshToken): string
    {
        // TODO: make sure to validate "refreshToken"
        $refreshTokenPayload = JWT::decode(
            $refreshToken,
            new Key(config('jwt.secret', 'secret'), 'HS256')
        );

        $refreshTokenState = JWT::decode(
            Cache::store('redis')->get($refreshTokenPayload->jti),
            new Key(config('jwt.secret', 'secret'), 'HS256')
        );

        return $this->genAccessToken(
            User::findOrFail($refreshTokenState->sub),
            $refreshTokenState->jti
        );
    }
}
