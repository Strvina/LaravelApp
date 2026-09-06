<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Expense::query()->latest('date');

        if (!$request->user()->isAdmin()) {
            $query->ownedBy($request->user()->id);
        }

        return response()->json($query->paginate(15));
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json($expense, 201);
    }
}
