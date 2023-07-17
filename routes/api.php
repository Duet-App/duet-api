<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;

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

Route::get('/tasks/', [TaskController::class, 'getTasks'])->middleware('auth:sanctum');

Route::get('/tasks/inbox', [TaskController::class, 'getInboxTasks'])->middleware('auth:sanctum');

Route::get('/tasks/today', [TaskController::class, 'getTodayTasks'])->middleware('auth:sanctum');

Route::post('/tasks/add', [TaskController::class, 'addTask'])->middleware('auth:sanctum');

Route::post('/tasks/add-task-to-today', [TaskController::class, 'addTaskToToday'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete'])->middleware('auth:sanctum');

Route::get('/tasks/{task}', [TaskController::class, 'show'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/edit', [TaskController::class, 'edit'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/schedule-on', [TaskController::class, 'scheduleOn'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/set-priority', [TaskController::class, 'setPriority'])->middleware('auth:sanctum');

Route::delete('/tasks/{task}/delete', [TaskController::class, 'delete'])->middleware('auth:sanctum');

Route::post('/tasks/{task}/add-subtask', [TaskController::class, 'addSubtask'])->middleware('auth:sanctum');

Route::get('/tasks/{task}/subtasks', [TaskController::class, 'fetchSubtasks'])->middleware('auth:sanctum');

Route::get('/projects/', [ProjectController::class, 'getProjects'])->middleware('auth:sanctum');

Route::post('/projects/add', [ProjectController::class, 'create'])->middleware('auth:sanctum');

Route::get('/projects/{project}', [ProjectController::class, 'show'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/move-to-project/{project}', [ProjectController::class, 'moveTaskToProject'])->middleware('auth:sanctum');

Route::post('/projects/{project}/add-task-to-project', [ProjectController::class, 'addTaskToProject'])->middleware('auth:sanctum');

Route::put('/projects/{project}/edit', [ProjectController::class, 'edit'])->middleware('auth:sanctum');

Route::put('/tasks/{task}/toggle-today', [TaskController::class, 'toggleToday'])->middleware('auth:sanctum')->middleware('auth:sanctum');