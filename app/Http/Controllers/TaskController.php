<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Tag;
use App\Models\Subtask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    public function getTasks() {
        $tasks = auth()->user()->tasks()->with(['project', 'tags', 'subtasks' => function($q) {
            $q->orderBy('order', 'asc');
        }])->get();
        return ['tasks' => $tasks];
    }

    public function getInboxTasks() {
        $tasks = auth()->user()->tasks()->where('project_id', null)->where(
            function(Builder $query) {
                return $query->where('is_today', false)
                             ->orWhere('status', 'T');
            }
        )->get();
        return ['tasks' => $tasks];
    }

    public function getTodayTasks() {
        $tasks = auth()->user()->tasks()
            ->where(function (Builder $query) {
                return $query->where('status', 'N')
                             ->orWhere('status', 'W');
            })
            ->where('is_complete', false)
            ->with('project')->get();
        return ['tasks' => $tasks];
    }

    public function show($id) {
        $task = Task::with(
            ['project', 'tags', 'subtasks' => function($q) {
                $q->orderBy('order', 'asc');
            }]
        )->find($id);
        return ['task' => $task];
    }

    public function addTask(Request $request) {
        $addedTask = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);
        $task = Task::with(
            ['project', 'tags', 'subtasks' => function($q) {
                $q->orderBy('order', 'asc');
            }]
        )->find($addedTask->id);
        return ['task' => $task];
    }

    public function addTaskToToday(Request $request, Task $task) {
        $addedTask = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_date' => $request->scheduledDate,
            'due_date' => $request->dueDate,
            'status' => "N",
            'user_id' => $request->user()->id,
        ]);
        $task = Task::with(
            ['project', 'tags', 'subtasks' => function($q) {
                $q->orderBy('order', 'asc');
            }]
        )->find($addedTask->id);
        return ['task' => $task];
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

    public function setDue(Task $task, Request $request) {
        $task->due_date = $request->dueDate;
        $task->save();
        return ['success' => '1'];
    }

    public function changeTaskStatus(Task $task, Request $request) {
        $task->status = $request->status;
        if($request->status == "C" || $request->status == "D") {
            $task->completed_on = Carbon::now();
        }
        $task->save();
        $task->refresh();
        return ['success' => '1', 'task' => $task];
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
        $addedSubtask = Subtask::create([
            'title' => $request->title,
            'order' => $maxCount,
            'task_id' => $task->id,
            'user_id' => auth()->user()->id
        ]);
        $subtask = $task->subtasks()->find($addedSubtask->id);
        return ['subtask' => $subtask];
    }

    public function fetchSubtasks(Task $task, Request $request) {
        return ['subtasks' => $task->subtasks()->orderBy('order', 'asc')->get()];
    }

    public function reorderSubtasks(Task $task, Request $request) {
        $subtasks = $task->subtasks()->get()->sortBy('order');
        $from = $request->from;
        $to = $request->to;
        $subtasks->splice($to, 0, $subtasks->splice($from, 1));
        $subtasks->each(function ($item, $key) {
            $item->order = $key + 1;
            $item->save();
        });
        return ['subtasks' => $task->subtasks()->orderBy('order', 'asc')->get()];
    }

    public function toggleCompleteSubtask(Task $task, Subtask $subtask) {
        $subtask->is_complete = ($subtask->is_complete == 1) ? 0 : 1;
        $subtask->save();
        return ['success' => '1'];
    }

    public function updateSubtask(Task $task, Subtask $subtask, Request $request) {
        $subtask->title = $request->title;
        $subtask->save();
        return ['success' => '1'];
    }

    public function deleteSubtask(Task $task, Subtask $subtask) {
        $subtask->delete();
        return ['subtasks' => $task->subtasks()->get()];
    }

    public function addTagsToTask(Request $request) {
        $task = Task::find($request->task_id);
        $task->tags()->detach();
        $task->tags()->attach($request->tag_id);
        $task = Task::with(
            ['project', 'tags', 'subtasks' => function($q) {
                $q->orderBy('order', 'asc');
            }]
        )->find($request->task_id);
        return ['tags' => $task->tags()->get()];
    }
}
