<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tasks/', [TaskController::class, 'getTasks']);

Route::get('/tasks/inbox', [TaskController::class, 'getInboxTasks']);

Route::get('/tasks/today', [TaskController::class, 'getTodayTasks']);

Route::post('/tasks/add', [TaskController::class, 'addTask']);

Route::post('/tasks/add-task-to-today', [TaskController::class, 'addTaskToToday']);

Route::put('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete']);

Route::get('/tasks/{task}', [TaskController::class, 'show']);

Route::put('/tasks/{task}/edit', [TaskController::class, 'edit']);

Route::delete('/tasks/{task}/delete', [TaskController::class, 'delete']);

Route::get('/projects/', [ProjectController::class, 'getProjects']);

Route::post('/projects/add', [ProjectController::class, 'create']);

Route::get('/projects/{project}', [ProjectController::class, 'show']);

Route::put('/tasks/{task}/move-to-project/{project}', [ProjectController::class, 'moveTaskToProject']);

Route::post('/projects/{project}/add-task-to-project', [ProjectController::class, 'addTaskToProject']);

Route::put('/projects/{project}/edit', [ProjectController::class, 'edit']);

Route::put('/tasks/{task}/toggle-today', [TaskController::class, 'toggleToday']);