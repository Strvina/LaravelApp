@extends('layouts.nav-layout')
@section('content')
    <div class="max-w-6xl mx-auto p-4">

        <h1 class="text-2xl font-bold mb-6">To-Do App</h1>

        <!-- Forma za dodavanje zadatka -->
        <form method="POST" action="{{ route('todo.save') }}" class="bg-white shadow-md rounded px-6 py-4 mb-6">
            @csrf

            <div class="mb-4">
                <label for="task" class="block text-gray-700 font-semibold mb-2">Zadatak</label>
                <input type="text" name="task" id="task" value="{{ old('task') }}" required
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Unesi novi zadatak">
            </div>

            <label for="priority">Priority</label>
            <select name="priority" id="priority" class="form-control">
                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
            </select>

            <label for="recurrence" class="px-4">
                Task se ponavlja?
                <input type="checkbox" name="is_recurring" id="is_recurring" value="1"
                    {{ old('is_recurring') ? 'checked' : '' }}>
            </label>

            <select name="recurrence" id="recurrence" class="form-control">
                <option value="daily" {{ old('recurrence') == 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ old('recurrence') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ old('recurrence') == 'monthly' ? 'selected' : '' }}>Monthly</option>
            </select>


            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded transition-colors">
                Sačuvaj
            </button>
        </form>

        <!-- Tabela sa svim taskovima -->
        <h2 class="text-xl font-bold mb-3">Lista zadataka</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="text-left px-4 py-2">#</th>
                        <th class="text-left px-4 py-2">Zadatak</th>
                        <th class="text-left px-4 py-2">Status</th>
                        <th class="text-left px-4 py-2">Prioritet</th>
                        <th class="text-left px-4 py-2">Recurring</th>
                        <th class="text-left px-4 py-2">Kreiran</th>
                        <th class="text-left px-4 py-2">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todo as $task)
                        <tr class="border-b hover:bg-gray-300 transition-colors">
                            <td class="px-4 py-2">{{ $task->id }}</td>
                            <td class="px-4 py-2">{{ $task->task }}</td>

                            <td
                                class="px-4 py-2
                            {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}
                            {{ $task->status === 'in_progress' ? 'italic animate-pulse text-blue-600' : '' }}">
                                <span
                                    class="
                                px-2 py-1 rounded-full text-sm
                                {{ $task->status === 'pending' ? 'bg-yellow-200 text-yellow-800' : '' }}
                                {{ $task->status === 'in_progress' ? 'bg-blue-200 text-blue-800' : '' }}
                                {{ $task->status === 'completed' ? 'bg-green-200 text-green-800' : '' }}
                            ">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>

                            <td class="px-4 py-2">
                                <span
                                    class="
                                px-2 py-1 rounded-full text-sm
                                {{ $task->priority === 'low' ? 'bg-gray-200 text-gray-800' : '' }}
                                {{ $task->priority === 'medium' ? 'bg-yellow-200 text-yellow-800' : '' }}
                                {{ $task->priority === 'high' ? 'bg-red-200 text-red-800' : '' }}
                            ">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>

                            <!-- DODATO: RECURRING -->
                            <td class="px-4 py-2">
                                @if ($task->is_recurring)
                                    <span class="px-2 py-1 rounded-full text-sm bg-blue-200 text-blue-800">
                                        {{ ucfirst($task->recurrence) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-4 py-2">{{ $task->created_at->format('d.m.Y H:i') }}</td>

                            <td class="px-4 py-2">
                                <a href="{{ route('todo.delete', $task->id) }}"
                                    class="text-red-600 hover:text-red-800 font-semibold">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-2 text-center text-gray-500">
                                Nema zadataka
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Drag & Drop Board -->
        <h2 class="text-xl font-bold mb-3">Board</h2>
        <div class="grid grid-cols-3 gap-4 mb-8">
            @foreach (['pending', 'in_progress', 'completed'] as $status)
                <div class="bg-gray-100 p-4 rounded shadow hover:bg-gray-300 transition-colors"
                    data-status="{{ $status }}" ondragover="allowDrop(event)" ondrop="drop(event)">
                    <h3 class="font-bold text-lg mb-4">
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </h3>

                    @foreach ($todo->where('status', $status) as $task)
                        <div class="bg-white p-2 mb-2 rounded shadow cursor-move
                            {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}
                            {{ $task->status === 'in_progress' ? 'italic animate-pulse text-blue-600' : '' }}"
                            draggable="true" ondragstart="drag(event)" data-id="{{ $task->id }}">
                            {{ $task->task }}

                            @if ($task->is_recurring)
                                <span class="text-xs text-blue-600 ml-2">
                                    ({{ ucfirst($task->recurrence) }})
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>
@endsection
