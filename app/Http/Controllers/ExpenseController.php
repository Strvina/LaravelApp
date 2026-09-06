<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Services\ActivityLogService;
use App\Services\ExpenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:income,expense'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $query = Expense::query()->with('user')->latest('date')->visibleTo($user);

        if ($filters['search'] ?? null) {
            $query->where('name', 'like', '%'.$filters['search'].'%');
        }

        if ($filters['type'] ?? null) {
            $query->ofType($filters['type']);
        }

        if ($filters['month'] ?? null) {
            $query->month($filters['month']);
        }

        if (($filters['min_amount'] ?? '') !== '') {
            $query->where('amount', '>=', $filters['min_amount']);
        }

        if (($filters['max_amount'] ?? '') !== '') {
            $query->where('amount', '<=', $filters['max_amount']);
        }

        $expenses = $query->paginate(10)->withQueryString();
        $summary = ExpenseService::monthlySummary($user->isAdmin() ? null : $user->id);

        return view('pages.expenses.index', array_merge([
            'expenses' => $expenses,
            'filters' => $filters,
        ], $summary));
    }

    public function addExpense(StoreExpenseRequest $request)
    {
        $this->authorize('create', Expense::class);

        $expense = ExpenseService::create($request->validated());

        ActivityLogService::log(
            'created',
            $expense,
            null,
            $expense->only(['name', 'amount', 'type', 'date', 'user_id']),
            'Expense created'
        );

        return redirect()->route('expenses.index')->with('success', 'Transaction added successfully.');
    }

    public function edit($id)
    {
        $expense = $this->resolveExpenseForUser($id);
        $this->authorize('update', $expense);

        return view('pages.expenses.edit', compact('expense'));
    }

    public function update(StoreExpenseRequest $request, $id)
    {
        $expense = $this->resolveExpenseForUser($id);
        $this->authorize('update', $expense);
        $oldValues = $expense->only(['name', 'amount', 'type', 'date', 'user_id']);

        $expense->update($request->validated());

        ActivityLogService::log(
            'updated',
            $expense,
            $oldValues,
            $expense->only(['name', 'amount', 'type', 'date', 'user_id']),
            'Expense updated'
        );

        return redirect()->route('expenses.index')->with('success', 'Transaction updated successfully.');
    }

    public function deleteExpense($id)
    {
        $expense = $this->resolveExpenseForUser($id);
        $this->authorize('delete', $expense);
        $oldValues = $expense->only(['name', 'amount', 'type', 'date', 'user_id']);
        ExpenseService::delete($expense);

        ActivityLogService::log(
            'deleted',
            $expense,
            $oldValues,
            null,
            'Expense deleted'
        );

        return redirect()->route('expenses.index')->with('success', 'Transaction deleted successfully.');
    }

    public function export()
    {
        $user = Auth::user();
        $query = Expense::query()->with('user')->latest('date')->visibleTo($user);

        $expenses = $query->get();
        $filename = 'expenses-export-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($expenses, $user) {
            $handle = fopen('php://output', 'w');
            $headers = ['Name', 'Amount', 'Type', 'Date'];

            if ($user->isAdmin()) {
                $headers[] = 'User';
            }

            fputcsv($handle, $headers);

            foreach ($expenses as $expense) {
                $row = [
                    $expense->name,
                    $expense->amount,
                    $expense->type,
                    $expense->date,
                ];

                if ($user->isAdmin()) {
                    $row[] = $expense->user?->name;
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function resolveExpenseForUser($id): Expense
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return Expense::findOrFail($id);
        }

        return Expense::ownedByKey($user->id, $id)->firstOrFail();
    }
}
