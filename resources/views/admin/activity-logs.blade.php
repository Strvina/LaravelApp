@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Admin module</p>
                <h1 class="page-title">Activity logs</h1>
                <p class="page-subtitle">A visible audit trail for user and system actions across the application.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="secondary-btn">Back to dashboard</a>
        </div>

        <div class="panel mb-8">
            <form method="GET" action="{{ route('admin.activity-logs') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <div>
                    <label for="user_id" class="field-label">User</label>
                    <select name="user_id" id="user_id" class="select-input">
                        <option value="">All users</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? '') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="action" class="field-label">Action</label>
                    <select name="action" id="action" class="select-input">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="model_type" class="field-label">Model</label>
                    <select name="model_type" id="model_type" class="select-input">
                        <option value="">All models</option>
                        @foreach ($modelTypes as $modelType)
                            <option value="{{ $modelType }}" @selected(($filters['model_type'] ?? '') === $modelType)>{{ class_basename($modelType) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="from" class="field-label">From</label>
                    <input type="date" name="from" id="from" value="{{ $filters['from'] ?? '' }}" class="text-input">
                </div>
                <div>
                    <label for="to" class="field-label">To</label>
                    <input type="date" name="to" id="to" value="{{ $filters['to'] ?? '' }}" class="text-input">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="primary-btn">Filter</button>
                    <a href="{{ route('admin.activity-logs') }}" class="secondary-btn">Reset</a>
                </div>
            </form>
        </div>

        <div class="panel overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-slate-200 text-left text-slate-500">
                        <tr>
                            <th class="px-3 py-3">When</th>
                            <th class="px-3 py-3">User</th>
                            <th class="px-3 py-3">Action</th>
                            <th class="px-3 py-3">Model</th>
                            <th class="px-3 py-3">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-b border-slate-100 align-top">
                                <td class="px-3 py-4 text-slate-600">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                                <td class="px-3 py-4 font-medium text-slate-900">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-3 py-4"><span class="badge bg-slate-100 text-slate-700">{{ $log->action }}</span></td>
                                <td class="px-3 py-4 text-slate-600">
                                    {{ class_basename($log->model_type ?? 'Record') }}
                                    @if ($log->model_id)
                                        #{{ $log->model_id }}
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-slate-600">{{ $log->description ?: 'No description provided.' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500">No activity logs recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </section>
@endsection
