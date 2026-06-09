<?php

use App\Http\Controllers\Api\HabitController;
use Illuminate\Support\Facades\Route;

Route::post('/habits', [HabitController::class, 'store']);
Route::get('/habits', [HabitController::class, 'index']);
Route::get('/habits/{id}', [HabitController::class, 'show']);
Route::put('/habits/{id}', [HabitController::class, 'update']);
Route::post('/habits/{id}/complete', [HabitController::class, 'complete']);
