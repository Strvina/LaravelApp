@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Admin module</p>
                <h1 class="page-title">Operations overview</h1>
                <p class="page-subtitle">A management view across users, products, notifications, finance, and recent activity.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.users.index') }}" class="secondary-btn">Manage users</a>
                <a href="{{ route('admin.activity-logs') }}" class="primary-btn">View activity logs</a>
            </div>
        </div>

        @if ($notifications->count() > 0)
            <div class="soft-panel mb-8">
                <h2 class="section-title">Notifications</h2>
                <div class="mt-4 grid gap-3">
                    @foreach ($notifications as $notification)
                        <div class="rounded-2xl border border-amber-200 bg-white px-4 py-3 text-sm text-amber-800">
                            {{ $notification->message }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div class="stat-card"><p class="stat-label">Users</p><p class="stat-value">{{ $totalUsers }}</p></div>
            <div class="stat-card"><p class="stat-label">Products</p><p class="stat-value">{{ $totalProducts }}</p></div>
            <div class="stat-card"><p class="stat-label">Monthly income</p><p class="stat-value">${{ number_format($monthlyRevenue, 2) }}</p></div>
            <div class="stat-card"><p class="stat-label">Monthly expenses</p><p class="stat-value">${{ number_format($monthlyExpense, 2) }}</p></div>
            <div class="stat-card"><p class="stat-label">Pending tasks</p><p class="stat-value">{{ $activeTasks }}</p></div>
            <div class="stat-card"><p class="stat-label">Completed tasks</p><p class="stat-value">{{ $completedTasksCount }}</p></div>
            <div class="stat-card"><p class="stat-label">Activity logs</p><p class="stat-value">{{ $activityCount }}</p></div>
            <div class="stat-card"><p class="stat-label">Low stock alerts</p><p class="stat-value">{{ $lowStockCount }}</p></div>
            <div class="stat-card"><p class="stat-label">Net balance</p><p class="stat-value">${{ number_format($netBalance, 2) }}</p></div>
        </div>

        <div class="mt-8 grid gap-8 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="panel">
                <h2 class="section-title">Recent activity</h2>
                <div class="mt-5 space-y-4">
                    @forelse ($recentActivities as $activity)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-semibold text-slate-900">{{ $activity->description ?? ucfirst($activity->action) }}</p>
                                <span class="badge bg-slate-100 text-slate-700">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-2 text-sm text-slate-600">
                                {{ $activity->user?->name ?? 'System' }} · {{ class_basename($activity->model_type ?? 'Record') }}
                                @if ($activity->model_id)
                                    #{{ $activity->model_id }}
                                @endif
                            </p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-sm text-slate-500">
                            No activity logs yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-8">
                <div class="panel">
                    <h2 class="section-title">Newest users</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($recentUsers as $user)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                </div>
                                <span class="badge {{ $user->role === 'admin' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-sm text-slate-500">
                                No users yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="panel">
                    <h2 class="section-title">Latest inventory items</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($recentProducts as $product)
                            <div class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $product->category ?: 'General' }}</p>
                                </div>
                                <span class="badge {{ $product->stock < 5 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $product->stock }} units
                                </span>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-sm text-slate-500">
                                No products yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="panel">
                <h2 class="section-title mb-4">User sign-ups per month</h2>
                <canvas id="userSignupsChart"></canvas>
            </div>
            <div class="panel">
                <h2 class="section-title mb-4">Expenses per month</h2>
                <canvas id="expensesChart"></canvas>
            </div>
            <div class="panel md:col-span-2">
                <h2 class="section-title mb-4">Completed tasks per month</h2>
                <canvas id="tasksChart"></canvas>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const months = @json($months);
        const userSignups = @json($userSignups);
        const expensesPerMonth = @json($expensesPerMonth);
        const completedTasks = @json($completedTasks);

        new Chart(document.getElementById('userSignupsChart'), {
            type: 'line',
            data: { labels: months, datasets: [{ label: 'User sign-ups', data: userSignups, borderColor: '#0f172a', backgroundColor: 'rgba(15, 23, 42, 0.08)', tension: 0.25, fill: true }] }
        });

        new Chart(document.getElementById('expensesChart'), {
            type: 'bar',
            data: { labels: months, datasets: [{ label: 'Expenses', data: expensesPerMonth, backgroundColor: 'rgba(234, 88, 12, 0.45)', borderRadius: 12 }] }
        });

        new Chart(document.getElementById('tasksChart'), {
            type: 'line',
            data: { labels: months, datasets: [{ label: 'Completed tasks', data: completedTasks, borderColor: '#059669', backgroundColor: 'rgba(5, 150, 105, 0.08)', tension: 0.25, fill: true }] }
        });
    </script>
@endsection
