@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Task module</p>
                <h1 class="page-title">Edit task</h1>
                <p class="page-subtitle">Adjust task wording, priority, and recurrence from a dedicated edit screen.</p>
            </div>
            <a href="{{ route('todo.index') }}" class="secondary-btn">Back to tasks</a>
        </div>

        <div class="panel max-w-4xl">
            <form method="POST" action="{{ route('todo.update', $todo->id) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="task" class="field-label">Task name</label>
                    <input type="text" name="task" id="task" value="{{ old('task', $todo->task) }}" class="text-input">
                    @error('task')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="priority" class="field-label">Priority</label>
                        <select name="priority" id="priority" class="select-input">
                            <option value="low" @selected(old('priority', $todo->priority) === 'low')>Low</option>
                            <option value="medium" @selected(old('priority', $todo->priority) === 'medium')>Medium</option>
                            <option value="high" @selected(old('priority', $todo->priority) === 'high')>High</option>
                        </select>
                        @error('priority')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="recurrence" class="field-label">Recurrence</label>
                        <select name="recurrence" id="recurrence" class="select-input">
                            <option value="">Does not repeat</option>
                            <option value="daily" @selected(old('recurrence', $todo->recurrence) === 'daily')>Daily</option>
                            <option value="weekly" @selected(old('recurrence', $todo->recurrence) === 'weekly')>Weekly</option>
                            <option value="monthly" @selected(old('recurrence', $todo->recurrence) === 'monthly')>Monthly</option>
                        </select>
                        @error('recurrence')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                    <input type="checkbox" name="is_recurring" id="is_recurring" value="1" @checked(old('is_recurring', $todo->is_recurring))>
                    Make this a recurring task
                </label>

                <button type="submit" class="primary-btn">Save changes</button>
            </form>
        </div>
    </section>
@endsection
