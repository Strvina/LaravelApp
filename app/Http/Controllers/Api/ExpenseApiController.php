<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseApiController extends Controller
{
    /**
     * List transactions
     *
     * Admins receive every user's transactions; everyone else only sees
     * their own.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Expense::query()->latest('date')->visibleTo($request->user());

        return response()->json($query->paginate(15));
    }

    /**
     * Log a transaction
     *
     * Creates an income or expense entry owned by the authenticated user.
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $this->authorize('create', Expense::class);

        $expense = Expense::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($expense, 201);
    }
}
