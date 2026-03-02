<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ToDoController;
use App\Http\Controllers\ExpenseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('/');
})->middleware(['auth', 'verified'])->name('/');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/proizvodi', [ProductController::class,"allProducts"])->name('products.all');
Route::post('/product/save', [ProductController::class,"addProduct"])->name('product.add');
Route::get('/proizvod/{productName}', [ProductController::class, 'index'])->name('product.single');
Route::get('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');
Route::get('/', [HomepageController::class, 'index'])->name('homepage');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::get('/pages/todo', [ToDoController::class, 'index'])->name('todo.index');
Route::post('/pages/todo/save', [ToDoController::class, 'addTodo'])->name('todo.save');
Route::get('/pages/todo/delete/{id}', [ToDoController::class, 'delete'])->name('todo.delete');
Route::patch('/pages/todo/update-status/{id}', [ToDoController::class, 'updateStatus'])->name('todo.updateStatus');
Route::get('/pages/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::post('/pages/expenses/add', [ExpenseController::class, 'addExpense'])->name('expenses.add');
Route::delete('/pages/expenses/delete/{id}', [ExpenseController::class, 'deleteExpense'])->name('expenses.delete');

});


require __DIR__.'/auth.php';
