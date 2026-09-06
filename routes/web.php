<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ToDoController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [HomepageController::class, 'index'])->name('dashboard');
    Route::get('/homepage', [HomepageController::class, 'index'])->name('homepage');

    Route::get('/proizvodi', [ProductController::class, 'allProducts'])->name('products.all');
    Route::get('/proizvod/{productName}', [ProductController::class, 'index'])->name('product.single');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('/products/import', [ProductController::class, 'import'])
        ->middleware(AdminMiddleware::class)
        ->name('products.import');
    Route::get('/product/{id}/edit', [ProductController::class, 'edit'])
        ->middleware(AdminMiddleware::class)
        ->name('product.edit');
    Route::put('/product/{id}', [ProductController::class, 'update'])
        ->middleware(AdminMiddleware::class)
        ->name('product.update');
    Route::post('/product/save', [ProductController::class, 'addProduct'])
        ->middleware(AdminMiddleware::class)
        ->name('product.add');
    Route::delete('/product/delete/{id}', [ProductController::class, 'delete'])
        ->middleware(AdminMiddleware::class)
        ->name('product.delete');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

    Route::get('/pages/todo', [ToDoController::class, 'index'])->name('todo.index');
    Route::get('/pages/todo/{id}/edit', [ToDoController::class, 'edit'])->name('todo.edit');
    Route::put('/pages/todo/{id}', [ToDoController::class, 'update'])->name('todo.update');
    Route::post('/pages/todo/save', [ToDoController::class, 'addTodo'])->name('todo.save');
    Route::delete('/pages/todo/delete/{id}', [ToDoController::class, 'delete'])->name('todo.delete');
    Route::patch('/pages/todo/update-status/{id}', [ToDoController::class, 'updateStatus'])->name('todo.updateStatus');

    Route::get('/pages/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
    Route::post('/pages/expenses/add', [ExpenseController::class, 'addExpense'])->name('expenses.add');
    Route::delete('/pages/expenses/delete/{id}', [ExpenseController::class, 'deleteExpense'])->name('expenses.delete');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/activity-logs', [AdminController::class, 'activityLogs'])->name('admin.activity-logs');
    Route::resource('admin/users', UserController::class, ['as' => 'admin'])->only(['index', 'edit', 'update', 'destroy']);
});

require __DIR__ . '/auth.php';
