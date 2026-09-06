<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreToDoRequest;
use App\Http\Requests\UpdateToDoRequest;
use App\Models\ToDo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoApiController extends Controller
{
    /**
     * List todos
     *
     * Admins receive every user's todos; everyone else only sees their own.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ToDo::query()->latest()->visibleTo($request->user());

        return response()->json($query->paginate(15));
    }

    /**
     * Create a todo
     *
     * Creates a new task owned by the authenticated user, starting in the
     * "pending" status.
     */
    public function store(StoreToDoRequest $request): JsonResponse
    {
        $todo = ToDo::create([
            ...$request->validated(),
            'status' => 'pending',
            'user_id' => $request->user()->id,
            'is_recurring' => $request->boolean('is_recurring'),
            'recurrence' => $request->boolean('is_recurring') ? $request->input('recurrence') : null,
        ]);

        return response()->json($todo, 201);
    }

    /**
     * Update a todo
     *
     * Owner or admin only.
     */
    public function update(UpdateToDoRequest $request, ToDo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $todo->update([
            ...$request->validated(),
            'is_recurring' => $request->boolean('is_recurring'),
            'recurrence' => $request->boolean('is_recurring') ? $request->input('recurrence') : null,
        ]);

        return response()->json($todo);
    }

    /**
     * Move a todo between statuses
     *
     * Owner or admin only.
     */
    public function updateStatus(Request $request, ToDo $todo): JsonResponse
    {
        $this->authorize('update', $todo);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $todo->update($validated);

        return response()->json($todo);
    }

    /**
     * Delete a todo
     *
     * Soft-deletes the task. Owner or admin only.
     */
    public function destroy(ToDo $todo): JsonResponse
    {
        $this->authorize('delete', $todo);
        $todo->delete();

        return response()->json(['deleted' => true]);
    }
}
