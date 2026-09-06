<?php

use App\Http\Controllers\Api\ExpenseApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TodoApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/todos', [TodoApiController::class, 'index']);
    Route::post('/todos', [TodoApiController::class, 'store']);
    Route::put('/todos/{todo}', [TodoApiController::class, 'update']);
    Route::patch('/todos/{todo}/status', [TodoApiController::class, 'updateStatus']);
    Route::delete('/todos/{todo}', [TodoApiController::class, 'destroy']);

    Route::get('/products', [ProductApiController::class, 'index']);

    Route::get('/expenses', [ExpenseApiController::class, 'index']);
    Route::post('/expenses', [ExpenseApiController::class, 'store']);
});
