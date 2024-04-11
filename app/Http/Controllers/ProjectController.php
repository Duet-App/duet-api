<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ProjectController extends Controller
{
    public function getProjects() {
        $projects = auth()->user()->projects()->withCount([
            'tasks',
            'tasks as completed_tasks' => function (Builder $query) {
                $query->where('status', 'C')->orWhere('status', 'D');
            }
        ])->with(['notes'])->get();
        return ['projects' => $projects];
    }

    public function create(Request $request) {
        $addedProject = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $request->user()->id,
        ]);

        $project = Project::withCount([
            'tasks',
            'tasks as completed_tasks' => function (Builder $query) {
                $query->where('status', 'C')->orWhere('status', 'D');
            }
        ])->find($addedProject->id);

        return ['project' => $project];
    }

    public function show(Project $project) {
        $project = Project::with(['tasks.tags', 'notes'])->find($project->id);
        // $project = Project::find($project->id);
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
        $task = Task::where('id', $addedTask->id)->with(['project', 'tags', 'subtasks' => function($q) {
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

    public function toggleAsComplete(Project $project, Request $request) {
        if($project->isComplete == 0) {
            $project->completed_on = Carbon::now();
        }
        $project->isComplete = ($project->isComplete == 1) ? 0 : 1;
        $project->save();
        return ['success' => '1'];
    }

    public function toggleArchive(Project $project, Request $request) {
        if($project->is_archived == 0) {
            $project->archived_on = Carbon::now();
        }
        $project->is_archived = ($project->is_archived == 1) ? 0 : 1;
        $project->save();
        return ['success' => '1'];
    }
}
