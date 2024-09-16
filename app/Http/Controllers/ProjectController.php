<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function getProjects() {
        $projects = auth()->user()->projects()->withCount([
            'tasks',
            'tasks as completed_tasks' => function (Builder $query) {
                $query->where('status', 'C')->orWhere('status', 'D');
            }
        ])->where('isComplete', false)->where('is_archived', false)->orderBy('updated_at', 'desc')->get();
        return ['projects' => $projects];
    }

    public function getArchivedProjects() {
        $projects = auth()->user()->projects()->withCount([
            'tasks',
            'tasks as completed_tasks' => function (Builder $query) {
                $query->where('status', 'C')->orWhere('status', 'D');
            }
        ])->where('isComplete', false)->where('is_archived', true)->orderBy('updated_at', 'desc')->get();
        return ['projects' => $projects];
    }

    public function getCompletedProjects() {
        $projects = auth()->user()->projects()->withCount([
            'tasks',
            'tasks as completed_tasks' => function (Builder $query) {
                $query->where('status', 'C')->orWhere('status', 'D');
            }
        ])->where('isComplete', true)->where('is_archived', false)->orderBy('updated_at', 'desc')->get();
        return ['projects' => $projects];
    }

    public function getDashboardProjects() {
        $projects = auth()->user()->projects()->withCount([
            'tasks',
            'tasks as completed_tasks' => function (Builder $query) {
                $query->where('status', 'C')->orWhere('status', 'D');
            }
        ])->orderBy('updated_at', 'desc')->take(3)->get();
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
        $project = Project::with(['tasks' => function (Builder $query) {
            $query->where('status', 'T')->orWhere('status', 'N')->orWhere('status', 'W')->orderBy('created_at', 'desc');
        }, 'tasks.tags', 'notes'])->find($project->id);
        // $project = Project::find($project->id);
        return ['project' => $project];
    }

    public function fetchCompletedTasks(Project $project) {
        $project = Project::with(['tasks' => function (Builder $query) {
            $query->where('status', 'D')->orWhere('status', 'C')->orderBy('completed_on', 'desc');
        }, 'tasks.tags', 'notes'])->find($project->id);
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

    public function exportToDuetPouchDB(Project $project, Request $request) {

        // Fetch all the project items
        $currentProject = Project::with(['tasks', 'tasks.tags', 'notes', 'tasks.subtasks'])->find($project->id);

        // Generate the Project UUID to construct the export JSON
        $projectId = (string) Str::uuid();

        // Create the array that will be exported
        $exportProject = [];

        // Add the project ID
        $exportProject["_id"] = $projectId;

        // Add project meta
        $exportProject["title"] = $currentProject->title;
        $exportProject["description"] = $currentProject->description;
        $exportProject["timestamps"] = [
            "created" => $currentProject->created_at,
            "updated" => $currentProject->updated_at,
        ];
        $exportProject["status"] = "In progress";
        $exportProject["type"] = "project";
        $exportProject["tasks"] = [];
        $exportProject["notes"] = [];

        // Create array for task
        $exportTasks = [];

        // Add project tasks
        foreach($currentProject->tasks as $i => $currentTask) {
            $taskId = (string) Str::uuid();
            $task = [];
            $task["_id"] = $taskId;
            $task["type"] = "task";
            $task["title"] = $currentTask->title;
            $task["description"] = $currentTask->description;
            $task["status"] = $currentTask->status == "T" ? "Todo" : ( $currentTask->status == "N" ? "Next" : ( $currentTask->status == "W" ? "Waiting" : ( $currentTask->status == "D" ? "Done" : ($currentTask->status == "C" ? "Cancelled" : "Todo") ) ) );
            $task["due_date"] = $currentTask->due_date;
            $task["scheduled_date"] = $currentTask->scheduled_date;
            $task["timestamps"] = [
                "created" => $currentTask->created_at,
                "updated" => $currentTask->updated_at,
                "completed" => $currentTask->status == "D" || $currentTask->status == "C" ? $currentTask->completed_on : null
            ];
            $task["subtasks"] = [];
            foreach($currentTask->subtasks as $j => $currentSubtask) {
                $subtask = [];
                $subtask["id"] = (string) Str::uuid();
                $subtask["title"] = $currentSubtask->title;
                $subtask["order"] = $j + 1;
                $subtask["complete"] = $currentSubtask->is_complete == 0 ? false : true;
                array_push($task["subtasks"], $subtask);
            }
            array_push($exportProject["tasks"], $taskId);
            $task["tags"] = [];
            foreach($currentTask->tags as $tag) {
                array_push($task["tags"], $tag->title);
            }
            $task["project_id"] = $projectId;
            array_push($exportTasks, $task);
        }

        // Create array for notes
        $exportNotes = [];

        // Add project notes
        foreach($currentProject->notes as $i => $currentNote) {
            $noteId = (string) Str::uuid();
            $note = [];
            $note["_id"] = $noteId;
            $note["type"] = "note";
            $note["title"] = $currentNote->title;
            $note["description"] = $currentNote->contents;
            $note["timestamps"] = [
                "created" => $currentNote->created_at,
                "updated" => $currentNote->updated_at
            ];
            array_push($exportProject["notes"], $noteId);
            $note["project_id"] = $projectId;
            array_push($exportNotes, $note);
        }

        // Return the constructed array as JSON
        return array_merge(array_merge([$exportProject], $exportTasks), $exportNotes);
    }
}
