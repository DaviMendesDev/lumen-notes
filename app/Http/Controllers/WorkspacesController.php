<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

class WorkspacesController extends Controller
{
    public function list()
    {
        return $this->response->success('All user workspaces', Auth::user()->workspaces()->get()->toArray());
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->prepareNewWorkspace(
            request()->json('name', 'untitled workspace')
        );

        return $this->response->success('Workspace created successfully');
    }

    public function roles($workspace)
    {
        /** @var Workspace $workspace */
        $workspace = Workspace::query()->findOrFail($workspace);

        return $this->response->success('All workspace roles', [
            'roles' => $workspace->roles()->with('permissions')->get()
        ]);
    }

    public function members($workspace)
    {
        /** @var Workspace $workspace */
        $workspace = Workspace::query()->findOrFail($workspace);

        return $this->response->success('All workspace members', [
            'members' => $workspace->members()->with('roles')->get()
        ]);
    }
}
