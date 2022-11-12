<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    public function update($user, Note $note)
    {
        /** @var User $user */
        return $user->isMine($note);
    }

    public function delete($user, Note $note)
    {
        /** @var User $user */
        return $user->isMine($note);
    }
}
