@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Task module</p>
                <h1 class="page-title">Task board</h1>
                <p class="page-subtitle">Track priorities, recurring work, and progress from a table view and a drag-and-drop
                    status board.</p>
            </div>
        </div>

        <div class="grid gap-8 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="panel">
                <h2 class="section-title">Add a task</h2>
                <p class="mt-2 text-sm text-slate-600">Create one-off or recurring work items for your personal or admin
                    workflow.</p>

                <form method="POST" action="{{ route('todo.save') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="task" class="field-label">Task name</label>
                        <input type="text" name="task" id="task" value="{{ old('task') }}" required
                            class="text-input" placeholder="Prepare monthly inventory review">
                        @error('task')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="filter_priority" class="field-label">Priority</label>
                            <select name="priority" id="filter_priority" class="select-input">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="recurrence" class="field-label">Recurrence</label>
                            <select name="recurrence" id="recurrence" class="select-input">
                                <option value="">Does not repeat</option>
                                <option value="daily" {{ old('recurrence') == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ old('recurrence') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('recurrence') == 'monthly' ? 'selected' : '' }}>Monthly
                                </option>
                            </select>
                            @error('recurrence')
                                <p class="field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <label
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                        <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                            {{ old('is_recurring') ? 'checked' : '' }}>
                        Make this a recurring task
                    </label>

                    <button type="submit" class="primary-btn">Save task</button>
                </form>
            </div>

            <div class="panel overflow-hidden">
                <div class="mb-5 flex flex-col gap-4">
                    <div>
                        <h2 class="section-title">Task list</h2>
                        <p class="mt-2 text-sm text-slate-600">A quick overview of current tasks and their state.</p>
                    </div>

                    <form method="GET" action="{{ route('todo.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                        <div class="xl:col-span-2">
                            <label for="search" class="field-label">Search</label>
                            <input type="search" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                                class="text-input" placeholder="Find a task">
                        </div>

                        <div>
                            <label for="status" class="field-label">Status</label>
                            <select name="status" id="status" class="select-input">
                                <option value="">All statuses</option>
                                @foreach (['pending' => 'Pending', 'in_progress' => 'In progress', 'completed' => 'Completed'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="field-label">Priority</label>
                            <select name="priority" id="priority" class="select-input">
                                <option value="">All priorities</option>
                                @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="sort" class="field-label">Sort</label>
                            <select name="sort" id="sort" class="select-input">
                                <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest first</option>
                                <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest first</option>
                            </select>
                        </div>

                        @if (auth()->user()->isAdmin())
                            <div>
                                <label for="scope" class="field-label">Owner</label>
                                <select name="scope" id="scope" class="select-input">
                                    <option value="all" @selected(($filters['scope'] ?? 'all') === 'all')>All users</option>
                                    <option value="mine" @selected(($filters['scope'] ?? '') === 'mine')>My tasks</option>
                                </select>
                            </div>
                        @endif

                        <div class="flex items-end gap-2 md:col-span-2 xl:col-span-5">
                            <button type="submit" class="primary-btn">Apply filters</button>
                            <a href="{{ route('todo.index') }}" class="secondary-btn">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="border-b border-slate-200 text-left text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Task</th>
                                @if (auth()->user()->isAdmin())
                                    <th class="px-3 py-3">Owner</th>
                                @endif
                                <th class="px-3 py-3">Status</th>
                                <th class="px-3 py-3">Priority</th>
                                <th class="px-3 py-3">Repeat</th>
                                <th class="px-3 py-3">Created</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($todo as $task)
                                <tr class="border-b border-slate-100 align-top">
                                    <td class="px-3 py-4 font-medium text-slate-900">{{ $task->task }}</td>
                                    @if (auth()->user()->isAdmin())
                                        <td class="px-3 py-4 text-slate-600">{{ $task->user->name }}</td>
                                    @endif
                                    <td class="px-3 py-4">
                                        <span
                                            class="badge
                                            {{ $task->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $task->status === 'in_progress' ? 'bg-sky-100 text-sky-800' : '' }}
                                            {{ $task->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4">
                                        <span
                                            class="badge
                                            {{ $task->priority === 'low' ? 'bg-slate-100 text-slate-700' : '' }}
                                            {{ $task->priority === 'medium' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $task->priority === 'high' ? 'bg-rose-100 text-rose-700' : '' }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-slate-600">
                                        {{ $task->is_recurring ? ucfirst($task->recurrence) : 'No' }}</td>
                                    <td class="px-3 py-4 text-slate-600">{{ $task->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="px-3 py-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('todo.edit', $task->id) }}" class="secondary-btn">Edit</a>
                                            <form method="POST" action="{{ route('todo.delete', $task->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="danger-btn"
                                                    onclick="return confirm('Delete this task?')">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}"
                                        class="px-3 py-8 text-center text-slate-500">
                                        No tasks yet. Add the first one from the form.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($todo->hasPages())
                    <div class="mt-5">
                        {{ $todo->links() }}
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-8">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Kanban board</h2>
                    <p class="page-subtitle">Drag cards between columns to update their status without reloading the form.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                @foreach (['pending', 'in_progress', 'completed'] as $status)
                    <div class="task-lane
                        {{ $status === 'pending' ? 'task-lane-pending' : '' }}
                        {{ $status === 'in_progress' ? 'task-lane-progress' : '' }}
                        {{ $status === 'completed' ? 'task-lane-completed' : '' }}"
                        data-task-lane data-status="{{ $status }}">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-lg font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $status)) }}</h3>
                            <span
                                class="badge bg-white/80 text-slate-600">{{ $boardTodo->where('status', $status)->count() }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">
                            {{ $status === 'pending' ? 'Queued and waiting to be started.' : '' }}
                            {{ $status === 'in_progress' ? 'Currently active and moving forward.' : '' }}
                            {{ $status === 'completed' ? 'Finished work ready for review.' : '' }}
                        </p>
                        <div class="mt-4 space-y-3">
                            @foreach ($boardTodo->where('status', $status) as $task)
                                <div class="task-card select-none" draggable="true" data-task-card
                                    data-id="{{ $task->id }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="font-semibold text-slate-900">{{ $task->task }}</p>
                                        <span
                                            class="badge
                                            {{ $task->priority === 'low' ? 'bg-slate-100 text-slate-700' : '' }}
                                            {{ $task->priority === 'medium' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $task->priority === 'high' ? 'bg-rose-100 text-rose-700' : '' }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </div>

                                    <div
                                        class="mt-3 flex items-center justify-between text-xs uppercase tracking-[0.2em] text-slate-400">
                                        <span>Drag to move</span>
                                        <span>#{{ $task->id }}</span>
                                    </div>

                                    @if ($task->is_recurring)
                                        <p class="mt-2 text-xs font-medium uppercase tracking-[0.2em] text-sky-700">
                                            Recurs {{ $task->recurrence }}
                                        </p>
                                    @endif

                                </div>
                            @endforeach

                            @if ($boardTodo->where('status', $status)->isEmpty())
                                <div
                                    class="rounded-2xl border border-dashed border-slate-200 bg-white/60 p-4 text-sm text-slate-500">
                                    No tasks in this column.
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
