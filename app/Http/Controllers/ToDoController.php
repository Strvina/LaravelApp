<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreToDoRequest;
use App\Models\ToDo;
use App\Services\TodoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ToDoController extends Controller
{
    public function index()
    {
        $todo = TodoService::listAllByUser(Auth::id());
        return view('pages.todo.index', compact('todo'));
    }

    public function addTodo(StoreToDoRequest $request)
    {
        TodoService::create($request->validated());
        return redirect()->route('todo.index');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $todo = ToDo::ownedByKey(Auth::id(), $id)->firstOrFail();
        TodoService::updateStatus($todo, $request->status);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $todo = ToDo::ownedByKey(Auth::id(), $id)->firstOrFail();
        TodoService::delete($todo);

        return redirect()->route('todo.index');
    }
}
