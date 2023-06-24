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

    public function show(Task $task) {
        return ['task' => $task];
    }

    public function addTask(Request $request) {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return ['success' => '1'];
    }

    public function toggleComplete(Task $task, Request $request) {
        $task->is_complete = ($task->is_complete == 1) ? 0 : 1;
        $task->save();
        return ['success' => '1'];
    }

    public function edit(Task $task, Request $request) {
        if($request->title)
            $task->title = $request->title;
        if($request->description)
            $task->description = $request->description;
        $task->save();
        return ['success' => '1'];
    }

    public function delete(Task $task) {
        $task->delete();
        return ['success' => '1'];
    }
}
