@extends('layouts.nav-layout')

@section('content')
<div class="max-w-4xl mx-auto p-4">

    <h1 class="text-2xl font-bold mb-4">Mini Budget Tracker</h1>

    <!-- Summary -->
    <div class="flex justify-between mb-6">
        <div class="p-4 bg-green-100 rounded shadow">
            <p class="font-semibold">Income</p>
            <p class="text-green-700 font-bold">${{ $totalIncome }}</p>
        </div>
        <div class="p-4 bg-red-100 rounded shadow">
            <p class="font-semibold">Expense</p>
            <p class="text-red-700 font-bold">${{ $totalExpense }}</p>
        </div>
        <div class="p-4 bg-blue-100 rounded shadow">
            <p class="font-semibold">Balance</p>
            <p class="text-blue-700 font-bold">${{ $balance }}</p>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('expenses.add') }}" class="mb-6 bg-white shadow-md rounded p-4">
        @csrf
        <div class="grid grid-cols-4 gap-4">
            <input type="text" name="name" placeholder="Name" class="border rounded px-2 py-1" required>
            <input type="number" step="0.01" name="amount" placeholder="Amount" class="border rounded px-2 py-1" required>
            <div class="flex items-center gap-4">
    <label class="flex items-center gap-1">
        <input type="radio" name="type" value="income" checked>
        Income
    </label>
    <label class="flex items-center gap-1">
        <input type="radio" name="type" value="expense">
        Expense
    </label>
</div>

            <input type="date" name="date" class="border rounded px-2 py-1" required>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded col-span-4 mt-2">Add</button>
        </div>
    </form>

    <!-- Table -->
    <table class="min-w-full bg-white shadow-md rounded mb-6">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-4 py-2 text-left">#</th>
                <th class="px-4 py-2 text-left">Name</th>
                <th class="px-4 py-2 text-left">Amount</th>
                <th class="px-4 py-2 text-left">Type</th>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
                <tr class="{{ $expense->type === 'income' ? 'bg-blue-50 text-blue-700 hover:bg-blue-100' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                    <td class="px-4 py-2">{{ $expense->id }}</td>
                    <td class="px-4 py-2">{{ $expense->name }}</td>
                    <td class="px-4 py-2">{{ $expense->amount }}</td>
                    <td class="px-4 py-2">{{ ucfirst($expense->type) }}</td>
                    <td class="px-4 py-2">{{ $expense->date }}</td>
                    <td class="px-4 py-2">
                        <form method="POST" action="{{ route('expenses.delete', $expense) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-500">No expenses yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="bg white shadow-md rounded p-4 h-64">
    <!-- Chart -->
    <canvas id="expensesChart" id="expensesChart"
        data-labels='@json($months)'
        data-income='@json($monthlyIncome)'
        data-expense='@json($monthlyExpense)'
        class="w-full h-64"></canvas>
</div>
</div>

@endsection
