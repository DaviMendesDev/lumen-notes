<?php

namespace Tests;

use App\Services\Common\AuthService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Cache;

class AuthServiceTest extends TestCase
{
    /** @test */
    public function auth_sign_in_should_return_access_token_and_refresh_token()
    {
        $authService = app(AuthService::class);
        $response = $authService->signin('davi.mendes.dev@gmail.com', 'admin123');
        $this->assertIsArray($response);
        $this->assertArrayHasKey('access', $response);
        $this->assertArrayHasKey('refresh', $response);

        Cache::store('redis')->set('test.refreshToken', $response['refresh'], 30 * 60);
        Cache::store('redis')->set('test.accessToken', $response['access'], 30 * 60);
    }

    /** @test */
    public function refresh_call_should_return_new_valid_access_token()
    {
        /** @var $authService AuthService */
        $authService = app(AuthService::class);
        $accessToken = $authService->refresh(
            Cache::store('redis')->get('test.refreshToken')
        );

        $decodedAccess = JWT::decode($accessToken, new Key(config('jwt.secret', 'secret'), 'HS256'));

        $this->assertObjectHasAttribute('sub', $decodedAccess);
        $this->assertObjectHasAttribute('iss', $decodedAccess);
        $this->assertObjectHasAttribute('aud', $decodedAccess);
        $this->assertObjectHasAttribute('exp', $decodedAccess);
        $this->assertObjectHasAttribute('iat', $decodedAccess);
        $this->assertObjectHasAttribute('refreshTokenId', $decodedAccess);
    }
}
