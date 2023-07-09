<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class TaskController extends Controller
{
    public function getTasks() {
        $tasks = auth()->user()->tasks()->get();
        return ['tasks' => $tasks];
    }

    public function getInboxTasks() {
        $tasks = auth()->user()->tasks()->where('project_id', null)->where('is_today', false)->get();
        return ['tasks' => $tasks];
    }

    public function getTodayTasks() {
        $tasks = auth()->user()->tasks()
            ->where(function (Builder $query) {
                return $query->where('is_today', true)
                             ->orWhereNotNull('scheduled_date');
            })
            ->where('is_complete', false)
            ->with('project')->get();
        return ['tasks' => $tasks];
    }

    public function show(Task $task) {
        return ['task' => $task];
    }

    public function addTask(Request $request) {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        return ['success' => '1'];
    }

    public function addTaskToToday(Request $request, Task $task) {
        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_today' => 1,
            'scheduled_date' => $request->scheduledDate,
            'user_id' => $request->user()->id,
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

    public function scheduleOn(Task $task, Request $request) {
        $task->scheduled_date = $request->scheduledDate;
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
