<?php

namespace App\Http\Controllers;
use App\Models\ToDo;
use Illuminate\Http\Request;

class ToDoController extends Controller
{
    public function index()
    {
        $todo = ToDo::all();
        return view("pages.todo.index", compact("todo"));
    }

    public function addTodo(Request $request)
    {
        //validacija
        $request->validate([
            "task" => "required|min:3|max:255"
        ]);

        //sacuvati u bazu
        ToDo::create($request->all());

        //preusmeriti nazad na todo stranu
        return redirect()->route("todo.index");
    }
    public function delete($id)
    {
        $todo = ToDo::find($id);
        $todo->delete();
        return redirect()->route("todo.index");
    }

    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed',
    ]);

    $task = ToDo::findOrFail($id);
    $task->status = $request->status;
    $task->save();

    return response()->json(['success' => true]);
}

}
