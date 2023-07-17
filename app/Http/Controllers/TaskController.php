<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Subtask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

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

    public function show($id) {
        $task = Task::with(['project', 'subtasks'])->find($id);
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
            'scheduled_date' => $request->scheduledDate,
            'due_date' => $request->dueDate,
            'user_id' => $request->user()->id,
        ]);
        return ['success' => '1'];
    }

    public function toggleComplete(Task $task, Request $request) {
        if($task->is_complete == 0) {
            $task->completed_on = Carbon::now();
        }
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

    public function setPriority(Task $task, Request $request) {
        $task->priority = $request->priority;
        $task->save();
        return ['success' => '1'];
    }

    public function edit(Task $task, Request $request) {
        if($request->title)
            $task->title = $request->title;
        if($request->description)
            $task->description = $request->description;
        if($request->scheduledDate)
            $task->scheduled_date = $request->scheduledDate;
        if($request->dueDate)
            $task->due_date = $request->dueDate;
        $task->save();
        return ['success' => '1'];
    }

    public function delete(Task $task) {
        $task->delete();
        return ['success' => '1'];
    }

    public function addSubtask(Task $task, Request $request) {
        $maxCount = $task->subtasks()->max('order') + 1;
        Subtask::create([
            'title' => $request->title,
            'order' => $maxCount,
            'task_id' => $task->id,
            'user_id' => auth()->user()->id
        ]);
        return ['success' => '1'];
    }

    public function fetchSubtasks(Task $task, Request $request) {
        return ['subtasks' => $task->subtasks()->get()];
    }
}
