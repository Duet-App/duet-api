<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register/', [AuthController::class, 'createUser']);

Route::post('/login/', [AuthController::class, 'loginUser']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tasks/', [TaskController::class, 'getTasks'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/tasks/inbox', [TaskController::class, 'getInboxTasks'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/tasks/next', [TaskController::class, 'getNextActions'])->middleware('auth:sanctum');

Route::get('/tasks/today', [TaskController::class, 'getTodayTasks'])->middleware('auth:sanctum');

Route::get('/tasks/waiting', [TaskController::class, 'getWaitingTasks'])->middleware('auth:sanctum');

Route::post('/tasks/add', [TaskController::class, 'addTask'])->middleware('auth:sanctum');

Route::post('/tasks/add-task-to-today', [TaskController::class, 'addTaskToToday'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/toggle-today', [TaskController::class, 'toggleToday'])->middleware('auth:sanctum');

Route::get('/tasks/{task}', [TaskController::class, 'show'])->middleware('auth:sanctum')->middleware('etag');

Route::put('/tasks/{task}/edit', [TaskController::class, 'edit'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/schedule-on', [TaskController::class, 'scheduleOn'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/set-priority', [TaskController::class, 'setPriority'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/set-due', [TaskController::class, 'setDue'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/change-status', [TaskController::class, 'changeTaskStatus'])->middleware('auth:sanctum');

Route::delete('/tasks/{task}/delete', [TaskController::class, 'delete'])->middleware('auth:sanctum');

Route::post('/tasks/{task}/add-subtask', [TaskController::class, 'addSubtask'])->middleware('auth:sanctum');

Route::get('/tasks/{task}/subtasks', [TaskController::class, 'fetchSubtasks'])->middleware('auth:sanctum');

Route::post('/tasks/{task}/subtasks/reorder', [TaskController::class, 'reorderSubtasks'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/subtasks/{subtask}/toggle-complete', [TaskController::class, 'toggleCompleteSubtask'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/subtasks/toggle-bulk-complete', [TaskController::class, 'toggleMultipleSubtasksAsComplete'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/subtasks/{subtask}/edit', [TaskController::class, 'updateSubtask'])->middleware('auth:sanctum');

Route::delete('/tasks/{task}/subtasks/{subtask}/delete', [TaskController::class, 'deleteSubtask'])->middleware('auth:sanctum');

Route::get('/projects/', [ProjectController::class, 'getProjects'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/projects/archived', [ProjectController::class, 'getArchivedProjects'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/projects/completed', [ProjectController::class, 'getCompletedProjects'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/projects/dashboard', [ProjectController::class, 'getDashboardProjects'])->middleware('auth:sanctum')->middleware('etag');

Route::post('/projects/add', [ProjectController::class, 'create'])->middleware('auth:sanctum');

Route::get('/projects/{project}', [ProjectController::class, 'show'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/projects/{project}/completed-tasks', [ProjectController::class, 'fetchCompletedTasks'])->middleware('auth:sanctum')->middleware('etag');

Route::put('/tasks/{task}/move-to-project/{project}', [ProjectController::class, 'moveTaskToProject'])->middleware('auth:sanctum');

Route::post('/projects/{project}/add-task-to-project', [ProjectController::class, 'addTaskToProject'])->middleware('auth:sanctum');

Route::put('/projects/{project}/edit', [ProjectController::class, 'edit'])->middleware('auth:sanctum');

Route::post('/projects/{project}/complete', [ProjectController::class, 'toggleAsComplete'])->middleware('auth:sanctum');

Route::post('/projects/{project}/archive', [ProjectController::class, 'toggleArchive'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/toggle-today', [TaskController::class, 'toggleToday'])->middleware('auth:sanctum');

Route::get('/tags/', [TagController::class, 'index'])->middleware('auth:sanctum')->middleware('etag');

Route::post('/tags/add', [TagController::class, 'add'])->middleware('auth:sanctum');

Route::post('/tags/add-to-task', [TaskController::class, 'addTagsToTask'])->middleware('auth:sanctum');

Route::get('/notes/', [NoteController::class, 'getNotes'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/notes/dashboard', [NoteController::class, 'getDashboardNotes'])->middleware('auth:sanctum')->middleware('etag');

Route::get('/notes/{note}', [NoteController::class, 'show'])->middleware('auth:sanctum')->middleware('etag');

Route::post('/notes/add', [NoteController::class, 'create'])->middleware('auth:sanctum');

Route::put('/notes/{note}/edit', [NoteController::class, 'edit'])->middleware('auth:sanctum');

Route::post('/projects/{project}/note/add-to-project', [NoteController::class, 'addToProject'])->middleware('auth:sanctum');

Route::put('/notes/{note}/move-to-project', [NoteController::class, 'moveToProject'])->middleware('auth:sanctum');
