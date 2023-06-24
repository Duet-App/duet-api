<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function getTasks() {
        $tasks = Task::all();
        return ['tasks' => $tasks];
    }

    public function addTask(Request $request) {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return ['success' => '1'];
    }
}
