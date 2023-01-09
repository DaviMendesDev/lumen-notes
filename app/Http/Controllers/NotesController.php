<?php

namespace App\Http\Controllers;

use App\Http\Requests\WriteIntoNoteRequest;
use App\Models\Note;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Lumen\Http\Request;

class NotesController extends Controller
{
    public function me(string|int $worspace = null)
    {
        /** @var User $user */
        $user = Auth::user();
        $notes = $worspace ? $user->withWorkspace(Workspace::query()->findOrFail($worspace))->notes() : $user->notes();


        return $this->response->success(
            'User accessible notes.',
            $notes->get()->values()->toArray()
        );
    }

    public function show(string|int $workspace, string|int $note) {
        $note = Note::findOrFail($note);

        $this->authorize('read', $note);

        return $this->response->success('Note detailed information.', $note->getAttributes());
    }

    public function create(string|int $workspace)
    {
        /** @var User $user */
        $user = Auth::user();
        $title = request()->json()->get('title', 'untitled');
        $this->authorize('createNote', Workspace::query()->findOrFail($workspace));

        $user->withWorkspace($workspace)->writeNewNote($title, '');

        return $this->response->success('Note created.');
    }

    public function writeInto(string $workspace, string $note, WriteIntoNoteRequest $form)
    {
        /** @var Note $note */
        $note = Note::findOrFail($note);

        $this->authorize('write', $note);

        $validated = $form->validate(request()->json()->all());

        /** @var User $user */
        $user = Auth::user();

        $user->writeInto($note, $validated['content']);
        $user->changeTitle($note, $validated['title']);

        return $this->response->success('Note updated.', $note->getAttributes());
    }

    public function delete(string $note)
    {
        /** @var Note $note */
        $note = Note::findOrFail($note);

        $this->authorize('delete', $note);

        /** @var User $user */
        $user = Auth::user();

        $user->sendToTrash($note);

        return $this->response->success('Note sent to trash.');
    }
}
