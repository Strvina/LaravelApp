<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $expenses = ExpenseService::listAllByUser($userId);
        $summary = ExpenseService::monthlySummary($userId);

        return view('pages.expenses.index', array_merge(['expenses' => $expenses], $summary));
    }

    public function addExpense(StoreExpenseRequest $request)
    {
        ExpenseService::create($request->validated());
        return redirect()->route('expenses.index');
    }

    public function deleteExpense($id)
    {
        $expense = Expense::ownedByKey(Auth::id(), $id)->firstOrFail();
        ExpenseService::delete($expense);

        return redirect()->route('expenses.index');
    }
}
