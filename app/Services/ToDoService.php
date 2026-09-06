<?php

namespace App\Services;

use App\Models\ToDo;
use Illuminate\Support\Facades\Auth;

class ToDoService
{
    public static function create(array $data): ToDo
    {
        return ToDo::create([
            'task' => $data['task'],
            'status' => $data['status'] ?? 'pending',
            'user_id' => $data['user_id'] ?? Auth::id(),
            'priority' => $data['priority'],
            'is_recurring' => $data['is_recurring'] ?? false,
            'recurrence' => ($data['is_recurring'] ?? false) ? ($data['recurrence'] ?? null) : null,
        ]);
    }

    public static function updateStatus(ToDo $todo, string $status): ToDo
    {
        $todo->update([
            'status' => $status,
        ]);

        return $todo->fresh();
    }

    public static function delete(ToDo $todo): void
    {
        $todo->delete();
    }
}
