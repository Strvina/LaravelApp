@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Admin module</p>
                <h1 class="page-title">Manage users</h1>
                <p class="page-subtitle">Review registered users, search by name or email, and update role assignments.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="secondary-btn">Back to dashboard</a>
        </div>

        <div class="panel">
            <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
                <div class="flex flex-col gap-3 md:flex-row">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email"
                        class="text-input">
                    <button type="submit" class="primary-btn">Search</button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-slate-200 text-left text-slate-500">
                        <tr>
                            <th class="px-3 py-3">ID</th>
                            <th class="px-3 py-3">Name</th>
                            <th class="px-3 py-3">Email</th>
                            <th class="px-3 py-3">Role</th>
                            <th class="px-3 py-3">Created</th>
                            <th class="px-3 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-4 text-slate-500">{{ $user->id }}</td>
                                <td class="px-3 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                                <td class="px-3 py-4 text-slate-600">{{ $user->email }}</td>
                                <td class="px-3 py-4">
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 text-slate-600">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-3 py-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="secondary-btn">Edit</a>
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="danger-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </section>
@endsection
