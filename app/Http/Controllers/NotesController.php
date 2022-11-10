<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
    public function create() {
        $note = new Note();
        $user = Auth::user();
        $note->owner()->associate($user);
    }
}
