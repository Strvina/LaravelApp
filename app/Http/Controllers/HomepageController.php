<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Products;
use App\Models\ToDo;
use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $todoQuery = ToDo::query()->visibleTo($user);
        $expenseQuery = Expense::query()->visibleTo($user);

        return view('pages.homepage', [
            'name' => $user->name,
            'taskStats' => [
                'pending' => (clone $todoQuery)->status('pending')->count(),
                'in_progress' => (clone $todoQuery)->status('in_progress')->count(),
                'completed' => (clone $todoQuery)->status('completed')->count(),
            ],
            'expenseStats' => [
                'income' => (clone $expenseQuery)->ofType('income')->sum('amount'),
                'expense' => (clone $expenseQuery)->ofType('expense')->sum('amount'),
            ],
            'productStats' => [
                'total' => Products::count(),
                'low_stock' => Products::lowStock()->count(),
            ],
            'recentTasks' => (clone $todoQuery)->latest()->limit(5)->get(),
        ]);
    }
}
