<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function index() {
        $tags = auth()->user()->tags()->get();
        return ['tags' => $tags];
    }

    public function add(Request $request) {
        $addedTag = Tag::create([
            'title' => $request->title,
            'type' => $request->type,
            'user_id' => $request->user()->id,
        ]);
        $tag = Tag::find($addedTag->id);
        return ['tag' => $tag];
    }
}
