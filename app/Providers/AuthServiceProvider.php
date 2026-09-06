<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Products;
use App\Models\ToDo;
use App\Models\User;
use App\Policies\ExpensePolicy;
use App\Policies\ProductPolicy;
use App\Policies\ToDoPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Expense::class => ExpensePolicy::class,
        Products::class => ProductPolicy::class,
        ToDo::class => ToDoPolicy::class,
        User::class => UserPolicy::class,
    ];
}
