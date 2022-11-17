<?php

namespace Tests\APIs;

use Tests\TestCase;

class AuthAPITest extends TestCase
{
    /** @test */
    public function should_return_new_guest_user()
    {
        $this->json('post', '/guest');

        $this->assertResponseOk();
        $this->response->assertJsonStructure([
            'message',
            'timestamp',
            'timestampReadable',
            'status',
            'data',
        ]);

        $data = $this->response->json('data');
        $this->assertArrayHasKey('accessToken', $data);
        $this->assertArrayHasKey('refreshToken', $data);
        $this->assertArrayHasKey('user', $data);

        $userInfo = $this->response->json('data.user');
        $this->assertArrayHasKey('name', $userInfo);
        $this->assertArrayHasKey('email', $userInfo);
    }

    /** @test */
    public function should_return_mine_user_info()
    {
        $this->createAuthenticatedUser();

        $this->json('get', '/me', [], [
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ]);

        $this->assertResponseOk();
        $this->response->assertJsonStructure([
            'message',
            'timestamp',
            'timestampReadable',
            'status',
            'data',
        ]);

        $userInfo = $this->response->json('data');
        $this->assertArrayHasKey('name', $userInfo);
        $this->assertArrayHasKey('email', $userInfo);
    }
}
