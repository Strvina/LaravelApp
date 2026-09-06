@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Finance module</p>
                <h1 class="page-title">Edit transaction</h1>
                <p class="page-subtitle">Update the details of this income or expense entry.</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="secondary-btn">Back to transactions</a>
        </div>

        <div class="panel max-w-2xl">
            <form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="grid gap-4 md:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="md:col-span-2">
                    <label for="name" class="field-label">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $expense->name) }}" class="text-input">
                </div>

                <div>
                    <label for="amount" class="field-label">Amount</label>
                    <input id="amount" name="amount" type="number" step="0.01" value="{{ old('amount', $expense->amount) }}" class="text-input">
                </div>

                <div>
                    <label for="date" class="field-label">Date</label>
                    <input id="date" name="date" type="date" value="{{ old('date', $expense->date) }}" class="text-input">
                </div>

                <div class="md:col-span-2">
                    <span class="field-label">Type</span>
                    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <label class="inline-flex w-full items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 sm:w-auto">
                            <input type="radio" name="type" value="income" @checked(old('type', $expense->type) === 'income')>
                            Income
                        </label>
                        <label class="inline-flex w-full items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 sm:w-auto">
                            <input type="radio" name="type" value="expense" @checked(old('type', $expense->type) === 'expense')>
                            Expense
                        </label>
                    </div>
                </div>

                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="primary-btn">Save changes</button>
                    <a href="{{ route('expenses.index') }}" class="secondary-btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
