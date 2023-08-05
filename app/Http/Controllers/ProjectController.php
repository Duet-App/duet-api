<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

class ProjectController extends Controller
{
    public function getProjects() {
        $projects = auth()->user()->projects()->withCount([
            'tasks',
            'tasks as completed_tasks' => function (Builder $query) {
                $query->where('status', 'C')->orWhere('status', 'D');
            }
        ])->get();
        return ['projects' => $projects];
    }

    public function create(Request $request) {
        Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        return ['success' => '1'];
    }

    public function show(Project $project) {
        // $project = Project::with('tasks')->find($project->id);
        $project = Project::find($project->id);
        return ['project' => $project];
    }

    public function moveTaskToProject(Task $task, Project $project) {
        $task->project_id = $project->id;
        $task->save();
        return ['success' => '1'];
    }

    public function addTaskToProject(Request $request, Project $project) {
        $addedTask = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'scheduled_date' => $request->scheduledDate,
            'due_date' => $request->dueDate,
            'user_id' => auth()->user()->id,
            'project_id' => $project->id
        ]);
        $task = Task::where('id', $addedTask->id)->with(['project', 'subtasks' => function($q) {
            $q->orderBy('order', 'asc');
        }])->first();
        return ['task' => $task];
    }

    public function edit(Project $project, Request $request) {
        if($request->title)
            $project->title = $request->title;
        if($request->description)
            $project->description = $request->description;
        $project->save();
        return ['success' => '1'];
    }
}
