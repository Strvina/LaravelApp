@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Admin module</p>
                <h1 class="page-title">Edit user</h1>
                <p class="page-subtitle">Update user identity details and change role access from the admin panel.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="secondary-btn">Back to users</a>
        </div>

        <div class="panel max-w-3xl">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="field-label">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="text-input">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="field-label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="text-input">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="field-label">Role</label>
                    <select id="role" name="role" class="select-input">
                        <option value="user" @selected(old('role', $user->role) === 'user')>User</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="primary-btn">Save changes</button>
                    <a href="{{ route('admin.users.index') }}" class="secondary-btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
