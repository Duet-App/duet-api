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

    public function getInboxTasks() {
        $tasks = Task::where('project_id', null)->where('is_today', false)->get();
        return ['tasks' => $tasks];
    }

    public function getTodayTasks() {
        $tasks = Task::where('is_today', true)->where('is_complete', false)->with('project')->get();
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

    public function addTaskToToday(Request $request, Task $task) {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_today' => 1
        ]);
        return ['success' => '1'];
    }

    public function toggleComplete(Task $task, Request $request) {
        $task->is_complete = ($task->is_complete == 1) ? 0 : 1;
        $task->save();
        return ['success' => '1'];
    }

    public function toggleToday(Task $task, Request $request) {
        $task->is_today = ($task->is_today == 1) ? 0 : 1;
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
