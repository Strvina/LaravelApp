<?php

namespace App\Providers;

use App\Models\ToDo;
use App\Policies\ToDoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ToDo::class => ToDoPolicy::class,
    ];
}
