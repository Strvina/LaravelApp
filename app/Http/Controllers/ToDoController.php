<?php

namespace App\Http\Controllers;
use App\Models\ToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreToDoRequest;

class ToDoController extends Controller
{
    public function index()
    {
        $todo = ToDo::where('user_id', Auth::id())->get();
        return view("pages.todo.index", compact("todo"));
    }

    public function addTodo(StoreToDoRequest $request)
    {
        //sacuvati u bazu
        ToDo::create([
            "task" => $request->task,
            "status" => "pending",
            "user_id" => Auth::id(),
            "priority" => "$request->priority",
            "is_recurring" => $request->is_recurring ? true : false,
            "recurrence" => $request->recurrence,
            "last_generated_at" => null,
        ]);

        //preusmeriti nazad na todo stranu
        return redirect()->route("todo.index");
    }
    public function delete($id)
    {
        $todo = ToDo::whereUserId(Auth::id())->whereKey($id)->firstOrFail();
        $todo->delete();
        return redirect()->route("todo.index");
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed',
    ]);

    $task = ToDo::whereUserId(Auth::id())->whereKey($id)->firstOrFail();
    $task->status = $request->status;

    $task->save();

    return response()->json(['success' => true]);
}

}
