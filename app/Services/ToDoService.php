<?php

namespace App\Services;

use App\Models\ToDo;
use Illuminate\Support\Facades\Auth;

class TodoService
{
    // Kreira novi zadatak
    public static function create(array $data)
    {
        return ToDo::create([
            'task' => $data['task'],
            'status' => 'pending',
            'user_id' => Auth::id(),
            'priority' => $data['priority'],
            'is_recurring' => $data['is_recurring'] ?? false,
            'recurrence' => $data['recurrence'] ?? null,
            'last_generated_at' => null,
        ]);
    }

    // Update statusa zadatka
    public static function updateStatus(ToDo $todo, string $status)
    {
        $todo->status = $status;
        $todo->save();
        return $todo;
    }

    // Brisanje zadatka
    public static function delete(ToDo $todo)
    {
        $todo->delete();
    }

    // Dohvata sve zadatke određenog korisnika
    public static function listAllByUser(int $userId)
    {
        return ToDo::ownedBy($userId)->orderBy('created_at', 'desc')->get();
    }
}
