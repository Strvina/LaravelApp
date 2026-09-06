@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="grid gap-8 lg:grid-cols-[1.35fr_0.65fr]">
            <div class="panel overflow-hidden">
                <div class="max-w-2xl">
                    <p class="badge bg-amber-100 text-amber-800">Laravel portfolio project</p>
                    <h1 class="mt-5 text-5xl font-bold leading-tight text-slate-900">
                        Centralize products, tasks, and cash flow in one workspace.
                    </h1>
                    <p class="mt-5 text-lg leading-8 text-slate-600">
                        Welcome back, {{ $name }}. OpsDesk is a small-business operations dashboard built to show
                        practical Laravel skills: authentication, role-aware admin tools, CRUD flows, charts,
                        scheduled jobs, validation, and automated tests.
                    </p>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.all') }}" class="primary-btn">Explore inventory</a>
                    <a href="{{ route('todo.index') }}" class="secondary-btn">Open task board</a>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <div class="stat-card">
                        <p class="stat-label">Pending tasks</p>
                        <p class="stat-value">{{ $taskStats['pending'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $taskStats['in_progress'] }} active and {{ $taskStats['completed'] }} completed.</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-label">Balance</p>
                        <p class="stat-value">{{ number_format($expenseStats['income'] - $expenseStats['expense'], 0) }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ number_format($expenseStats['income'], 0) }} income minus {{ number_format($expenseStats['expense'], 0) }} expense.</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-label">Inventory</p>
                        <p class="stat-value">{{ $productStats['total'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $productStats['low_stock'] }} products are low on stock.</p>
                    </div>
                </div>
            </div>

            <div class="panel bg-slate-900 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-300">What this project shows</p>
                <div class="mt-6 space-y-4">
                    @forelse ($recentTasks as $task)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="text-base font-bold">{{ $task->task }}</h2>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-200">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm leading-6 text-slate-300">
                                {{ ucfirst($task->priority) }} priority
                                @if ($task->is_recurring)
                                    - recurs {{ $task->recurrence }}
                                @endif
                            </p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <h2 class="text-lg font-bold">No recent tasks</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Create your first task to start filling the workspace.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <article class="panel">
                <p class="stat-label">Inventory</p>
                <h2 class="mt-3 text-2xl font-bold">Filterable product catalog</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Search by category, brand, price range, and stock
                    status with a lightweight real-time filter experience.</p>
            </article>
            <article class="panel">
                <p class="stat-label">Tasks</p>
                <h2 class="mt-3 text-2xl font-bold">Drag-and-drop task board</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Track priorities, recurring work, and status updates
                    with a simple board plus scheduled recurring task generation.</p>
            </article>
            <article class="panel">
                <p class="stat-label">Finance</p>
                <h2 class="mt-3 text-2xl font-bold">Monthly income and expense view</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Capture transactions, calculate balances, and
                    visualize monthly trends through a chart-driven summary.</p>
            </article>
        </div>
    </section>
@endsection
