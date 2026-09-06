<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreToDoRequest;
use App\Http\Requests\UpdateToDoRequest;
use App\Models\ToDo;
use App\Services\ActivityLogService;
use App\Services\ToDoService as TodoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToDoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,in_progress,completed'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'sort' => ['nullable', 'in:newest,oldest'],
            'scope' => ['nullable', 'in:all,mine'],
        ]);

        $query = ToDo::query()
            ->with('user')
            ->search($filters['search'] ?? null)
            ->status($filters['status'] ?? null)
            ->priority($filters['priority'] ?? null);

        if (!$user->isAdmin() || ($filters['scope'] ?? null) === 'mine') {
            $query->ownedBy($user->id);
        }

        $query->orderBy('created_at', ($filters['sort'] ?? 'newest') === 'oldest' ? 'asc' : 'desc');

        $todo = (clone $query)->paginate(10)->withQueryString();
        $boardTodo = (clone $query)->get();

        return view('pages.todo.index', [
            'todo' => $todo,
            'boardTodo' => $boardTodo,
            'filters' => $filters,
        ]);
    }

    public function addTodo(StoreToDoRequest $request)
    {
        $todo = TodoService::create($request->validated());

        ActivityLogService::log(
            'created',
            $todo,
            null,
            $todo->only(['task', 'status', 'priority', 'is_recurring', 'recurrence', 'user_id']),
            'Todo created'
        );

        return redirect()->route('todo.index')->with('success', 'Task created successfully.');
    }

    public function edit($id)
    {
        $todo = $this->resolveTodoForUser($id);
        $this->authorize('update', $todo);

        return view('pages.todo.edit', compact('todo'));
    }

    public function update(UpdateToDoRequest $request, $id)
    {
        $todo = $this->resolveTodoForUser($id);
        $this->authorize('update', $todo);
        $oldValues = $todo->only(['task', 'priority', 'is_recurring', 'recurrence']);
        $validated = $request->validated();

        $todo->update([
            'task' => $validated['task'],
            'priority' => $validated['priority'],
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurrence' => $validated['is_recurring'] ?? false
                ? ($validated['recurrence'] ?? null)
                : null,
        ]);

        ActivityLogService::log(
            'updated',
            $todo,
            $oldValues,
            $todo->only(['task', 'priority', 'is_recurring', 'recurrence']),
            'Todo updated'
        );

        return redirect()->route('todo.index')->with('success', 'Task updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $todo = $this->resolveTodoForUser($id);
        $this->authorize('update', $todo);
        $oldValues = ['status' => $todo->status];
        TodoService::updateStatus($todo, $request->status);

        ActivityLogService::log(
            'updated_status',
            $todo,
            $oldValues,
            ['status' => $todo->status],
            'Todo status updated'
        );

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $todo = $this->resolveTodoForUser($id);
        $this->authorize('delete', $todo);
        $oldValues = $todo->only(['task', 'status', 'priority', 'is_recurring', 'recurrence', 'user_id']);
        TodoService::delete($todo);

        ActivityLogService::log(
            'deleted',
            $todo,
            $oldValues,
            null,
            'Todo deleted'
        );

        return redirect()->route('todo.index')->with('success', 'Task deleted successfully.');
    }

    protected function resolveTodoForUser($id): ToDo
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return ToDo::findOrFail($id);
        }

        return ToDo::ownedByKey($user->id, $id)->firstOrFail();
    }
}
