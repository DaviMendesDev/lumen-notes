<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy {
    public function createNote(User $user, Workspace $workspace)
    {
        return $user->canDo('create_note', $workspace);
    }
}