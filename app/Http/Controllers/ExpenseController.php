<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', Auth::id())->orderBy('date', 'desc')->get();
        $totalIncome = Expense::where('user_id', Auth::id())->where('type', 'income')->sum('amount');
        $totalExpense = Expense::where('user_id', Auth::id())->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Za grafikon: mesečne sume
        $months = [];
        $monthlyIncome = [];
        $monthlyExpense = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0,0,0,$i,1));
            $monthlyIncome[] = Expense::where('user_id', Auth::id())->where('type', 'income')->whereMonth('date', $i)->sum('amount');
            $monthlyExpense[] = Expense::where('user_id', Auth::id())->where('type', 'expense')->whereMonth('date', $i)->sum('amount');
        }

        return view('pages.expenses.index', compact(
            'expenses', 'totalIncome', 'totalExpense', 'balance',
            'months', 'monthlyIncome', 'monthlyExpense'
        ));
    }

    public function addExpense(StoreExpenseRequest $request)
    {
        Expense::create([
            "name"=>$request->name,
            "amount"=>$request->amount,
            "type"=>$request->type,
            "date"=>$request->date,
            "user_id"=>Auth::id()
        ]);

        return redirect()->route("expenses.index");
    }
    public function deleteExpense($id)
    {
        $expense=Expense::whereUserId(Auth::id())->whereKey($id)->firstOrFail();
        $expense->delete();
        return redirect()->route("expenses.index");
    }
}
