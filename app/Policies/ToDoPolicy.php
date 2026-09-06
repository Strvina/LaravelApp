<?php

namespace App\Policies;

use App\Models\ToDo;
use App\Models\User;

class ToDoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ToDo $todo): bool
    {
        return $user->isAdmin() || $todo->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ToDo $todo): bool
    {
        return $this->view($user, $todo);
    }

    public function delete(User $user, ToDo $todo): bool
    {
        return $this->view($user, $todo);
    }
}
