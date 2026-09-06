<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpenseApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TodoApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/todos', [TodoApiController::class, 'index']);
    Route::post('/todos', [TodoApiController::class, 'store']);
    Route::put('/todos/{todo}', [TodoApiController::class, 'update']);
    Route::patch('/todos/{todo}/status', [TodoApiController::class, 'updateStatus']);
    Route::delete('/todos/{todo}', [TodoApiController::class, 'destroy']);

    Route::get('/products', [ProductApiController::class, 'index']);

    Route::get('/expenses', [ExpenseApiController::class, 'index']);
    Route::post('/expenses', [ExpenseApiController::class, 'store']);
});
