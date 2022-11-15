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
    }
}
