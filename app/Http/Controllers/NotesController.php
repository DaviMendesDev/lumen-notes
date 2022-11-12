<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\UnauthorizedException;

class NotesController extends Controller
{
    public function me()
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->response->success('user\'s notes.', $user->notes()->get()->values()->toArray());
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        $title = request()->json()->get('title', 'untitled');
        $user->writeNewNote($title, '');

        return $this->response->success('Note created.');
    }

    public function update(string $note, UpdateNoteRequest $form)
    {
        /** @var Note $note */
        $note = Note::findOrFail($note);

        if (Gate::denies('update', $note)) {
            throw new UnauthorizedException('You are not authorized to change this resource.');
        }

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

        if (Gate::denies('delete', $note)) {
            throw new UnauthorizedException('You are not authorized to change this resource.');
        }

        /** @var User $user */
        $user = Auth::user();

        $user->sendToTrash($note);

        return $this->response->success('Note sent to trash.');
    }
}
