<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use App\Models\Workspace;

class NotePolicy
{
    public function write(User $user, Note $note)
    {
        return $user->canDo('write_note', $note->workspace);
    }

    public function read(User $user, Note $note)
    {
        return $user->canDo('read_note', $note->workspace);
    }

    public function delete($user, Note $note)
    {
        /** @var User $user */
        return $user->canDo('delete_note', $note->workspace);
    }
}
