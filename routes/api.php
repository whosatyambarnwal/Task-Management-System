<?php

use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Task endpoints
    Route::get('/tasks', [TaskApiController::class, 'index']);
    Route::get('/tasks/{task}', [TaskApiController::class, 'show']);
    Route::patch('/tasks/{task}/status', [TaskApiController::class, 'updateStatus']);
    Route::post('/tasks', [TaskApiController::class, 'store']);
    Route::put('/tasks/{task}', [TaskApiController::class, 'update']);

    // Auth user info
    Route::get('/user', function () {
        return response()->json([
            'success' => true,
            'data' => request()->user(),
        ]);
    });
});
