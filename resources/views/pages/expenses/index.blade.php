@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Finance module</p>
                <h1 class="page-title">Expense and income tracker</h1>
                <p class="page-subtitle">Monitor cash movement, compare totals, and review monthly performance trends.</p>
            </div>
            <a href="{{ route('expenses.export') }}" class="secondary-btn">Export CSV</a>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="stat-card">
                <p class="stat-label">Income</p>
                <p class="stat-value text-emerald-700">${{ number_format($totalIncome, 2) }}</p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Expense</p>
                <p class="stat-value text-rose-700">${{ number_format($totalExpense, 2) }}</p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Balance</p>
                <p class="stat-value text-slate-900">${{ number_format($balance, 2) }}</p>
            </div>
        </div>

        <div class="mt-8 grid gap-8 xl:grid-cols-[0.9fr_1.1fr]">
            <div class="panel min-w-0 overflow-hidden">
                <h2 class="section-title">Add transaction</h2>
                <p class="mt-2 text-sm text-slate-600">Store incoming revenue or outgoing costs with a date and type.</p>

                <form method="POST" action="{{ route('expenses.add') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="field-label">Name</label>
                        <input type="text" name="name" id="name" class="text-input" placeholder="Office rent" required>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="amount" class="field-label">Amount</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="text-input" placeholder="0.00" required>
                        </div>
                        <div>
                            <label for="date" class="field-label">Date</label>
                            <input type="date" name="date" id="date" class="text-input" required>
                        </div>
                    </div>

                    <div>
                        <span class="field-label">Type</span>
                        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            <label class="inline-flex w-full items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 sm:w-auto">
                                <input type="radio" name="type" value="income" checked>
                                Income
                            </label>
                            <label class="inline-flex w-full items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 sm:w-auto">
                                <input type="radio" name="type" value="expense">
                                Expense
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="primary-btn w-full sm:w-auto">Add transaction</button>
                </form>
            </div>

            <div class="panel min-w-0 overflow-hidden">
                <div class="mb-5 space-y-4">
                    <div>
                        <h2 class="section-title">Transaction history</h2>
                        <p class="mt-2 text-sm text-slate-600">A chronological view of your recorded finance entries.</p>
                    </div>

                    <form method="GET" action="{{ route('expenses.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                        <div class="xl:col-span-2">
                            <label for="search" class="field-label">Search</label>
                            <input type="search" name="search" id="search" value="{{ $filters['search'] ?? '' }}" class="text-input" placeholder="Find transaction">
                        </div>
                        <div>
                            <label for="type" class="field-label">Type</label>
                            <select name="type" id="type" class="select-input">
                                <option value="">All types</option>
                                <option value="income" @selected(($filters['type'] ?? '') === 'income')>Income</option>
                                <option value="expense" @selected(($filters['type'] ?? '') === 'expense')>Expense</option>
                            </select>
                        </div>
                        <div>
                            <label for="month" class="field-label">Month</label>
                            <select name="month" id="month" class="select-input">
                                <option value="">All months</option>
                                @foreach ($months as $index => $month)
                                    <option value="{{ $index + 1 }}" @selected(($filters['month'] ?? '') == $index + 1)>{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="primary-btn">Filter</button>
                            <a href="{{ route('expenses.index') }}" class="secondary-btn">Reset</a>
                        </div>
                        <div>
                            <label for="min_amount" class="field-label">Min amount</label>
                            <input type="number" step="0.01" name="min_amount" id="min_amount" value="{{ $filters['min_amount'] ?? '' }}" class="text-input">
                        </div>
                        <div>
                            <label for="max_amount" class="field-label">Max amount</label>
                            <input type="number" step="0.01" name="max_amount" id="max_amount" value="{{ $filters['max_amount'] ?? '' }}" class="text-input">
                        </div>
                    </form>
                </div>

                <div class="-mx-6 overflow-x-auto px-6 sm:mx-0 sm:px-0">
                    <table class="min-w-[40rem] text-sm sm:min-w-full">
                        <thead class="border-b border-slate-200 text-left text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Name</th>
                                @if (auth()->user()->isAdmin())
                                    <th class="px-3 py-3">User</th>
                                @endif
                                <th class="px-3 py-3">Amount</th>
                                <th class="px-3 py-3">Type</th>
                                <th class="px-3 py-3">Date</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expenses as $expense)
                                <tr class="border-b border-slate-100">
                                    <td class="px-3 py-4 font-medium text-slate-900">{{ $expense->name }}</td>
                                    @if (auth()->user()->isAdmin())
                                        <td class="px-3 py-4 text-slate-600">{{ $expense->user->name }}</td>
                                    @endif
                                    <td class="px-3 py-4 font-semibold {{ $expense->type === 'income' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        ${{ number_format($expense->amount, 2) }}
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="badge {{ $expense->type === 'income' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                            {{ ucfirst($expense->type) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-slate-600">{{ $expense->date }}</td>
                                    <td class="px-3 py-4">
                                        <form method="POST" action="{{ route('expenses.delete', $expense) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="danger-btn whitespace-nowrap">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="px-3 py-8 text-center text-slate-500">
                                        No transactions yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($expenses->hasPages())
                    <div class="mt-6">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="panel mt-8 h-[24rem]">
            <div class="mb-5">
                <h2 class="section-title">Monthly comparison</h2>
                <p class="mt-2 text-sm text-slate-600">Visualize income versus expense over the calendar year.</p>
            </div>

            <canvas id="expensesChart" data-labels='@json($months)' data-income='@json($monthlyIncome)'
                data-expense='@json($monthlyExpense)' class="h-full w-full"></canvas>
        </div>
    </section>
@endsection
