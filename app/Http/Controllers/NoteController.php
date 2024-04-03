<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Note;
use Illuminate\Support\Carbon;

class NoteController extends Controller
{
    public function getNotes() {
        $notes = auth()->user()->notes()->with(['project'])->get();
        return ['notes' => $notes];
    }

    public function create(Request $request) {
        $addedNote = Note::create([
            'title' => $request->title,
            'contents' => $request->contents,
            'user_id' => $request->user()->id,
        ]);

        $note = Note::find($addedNote->id);

        return ['note' => $note];
    }

    public function edit(Note $note, Request $request) {
        if($request->title) {
            $note->title = $request->title;
        }
        if($request->contents) {
            $note->contents = $request->contents;
        }
        $note->save();
        $note->refresh();
        return ['note' => $note];
    }

    public function moveToProject(Note $note, Request $request) {
        if($request->projectId) {
            $note->project_id = $request->projectId;
        }
        $note->save();
        $note->refresh();
        return ['note' => $note];
    }

    public function addToProject(Project $project, Request $request) {
        $addedNote = Note::create([
            'title' => $request->title,
            'contents' => $request->contents,
            'project_id' => $project->id,
            'user_id' => $request->user()->id,
        ]);

        $note = Note::find($addedNote->id);
        $project->fresh();

        return ['project' => $project, 'note' => $note];
    }
}
