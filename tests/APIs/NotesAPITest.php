<?php

namespace Tests\APIs;

use App\Models\User;
use App\Services\Common\AuthService;
use Tests\TestCase;

class NotesAPITest extends TestCase
{

    /** @test */
    public function should_return_all_user_notes()
    {
        /** @var User $user */
        $user = $this->createAuthenticatedUser();

        $user->withWorkspace($this->defaultWorkspace())->writeNewNote('untitled', 'any-content');
        $this->json('get', '/notes/me', [], [
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ]);

        $this->assertResponseOk();
        $this->response->assertJsonStructure([
            'message',
            'timestamp',
            'timestampReadable',
            'status',
            'data' => [
                '*' => [
                    'id',
                    'creator_id',
                    'title',
                    'created_at',
                    'updated_at'
                ]
            ],
        ]);
    }

    /** @test */
    public function should_insert_one_note()
    {
        /** @var User $user */
        $user = $this->createAuthenticatedUser();
        $this->json('post', '/notes/create', [
            'title' => 'my title',
            'content' => 'my content',
        ], [
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

        $freshNote = $user->notes()->firstOrFail()->getAttributes();

        $this->assertArrayHasKey('title', $freshNote);
        $this->assertArrayHasKey('content', $freshNote);
        $this->assertEquals('my title', $freshNote['title']);
        $this->assertEquals('', $freshNote['content']);
    }

    /** @test */
    public function should_update_one_note()
    {
        /** @var User $user */
        $user = $this->createAuthenticatedUser();
        $user->withWorkspace($this->defaultWorkspace())->writeNewNote('untitled', '');

        $this->json('put', '/workspaces/' . $this->defaultWorkspace()->id . '/notes/' . $user->notes()->firstOrFail()->id, [
            'title' => 'my new title',
            'content' => 'my content',
        ], [
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ]);

        $this->assertEquals('asdf', $this->response->getContent());
        $this->assertResponseOk();
        $this->response->assertJsonStructure([
            'message',
            'timestamp',
            'timestampReadable',
            'status',
            'data',
        ]);

        $freshNote = $user->notes()->firstOrFail()->getAttributes();

        $this->assertArrayHasKey('title', $freshNote);
        $this->assertArrayHasKey('content', $freshNote);
        $this->assertEquals('my new title', $freshNote['title']);
        $this->assertEquals('my content', $freshNote['content']);
    }

    /** @test */
    public function should_delete_one_note()
    {
        /** @var User $user */
        $user = $this->createAuthenticatedUser();
        $user->prepareNewWorkspace('My workspace');
        $user->withWorkspace($this->defaultWorkspace())->writeNewNote('untitled', '');
        $this->assertNotEmpty($user->notes()->get()->toArray());

        $this->json('delete', '/notes/' . $user->notes()->firstOrFail()->id, [], [
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

        $userNotes = $user->notes()->get();
        $this->assertEmpty($userNotes->toArray());
    }
}
