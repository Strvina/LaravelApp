<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->get();
        $totalIncome = Expense::where('type', 'income')->sum('amount');
        $totalExpense = Expense::where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Za grafikon: mesečne sume
        $months = [];
        $monthlyIncome = [];
        $monthlyExpense = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0,0,0,$i,1));
            $monthlyIncome[] = Expense::where('type', 'income')->whereMonth('date', $i)->sum('amount');
            $monthlyExpense[] = Expense::where('type', 'expense')->whereMonth('date', $i)->sum('amount');
        }

        return view('pages.expenses.index', compact(
            'expenses', 'totalIncome', 'totalExpense', 'balance',
            'months', 'monthlyIncome', 'monthlyExpense'
        ));
    }

    public function addExpense(Request $request)
    {
        $request->validate([
            "name"=>"required|string|max:255",
            "amount"=>"required|numeric",
            "type"=>"required|in:income,expense",
            "date"=>"required|date"
        ]);

        Expense::create($request->all());

        return redirect()->route("expenses.index");
    }
    public function deleteExpense($id)
    {
        $expense=Expense::findOrFail($id);
        $expense->delete();
        return redirect()->route("expenses.index");
    }
}
