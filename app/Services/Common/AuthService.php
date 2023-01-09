<?php

namespace App\Services\Common;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\UnauthorizedException;

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
//            'exp' => Carbon::now()->addDays(30)->timestamp,
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
        Cache::store('redis')->set($uuid, $token);

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
        $payload = JWT::decode($accessToken, new Key(config('jwt.secret', 'secret'), 'HS256'));

        if (! $userId = $payload->sub) {
            throw new UnauthorizedException("Token invalid!");
        }

        return User::findOrFail($userId);
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
