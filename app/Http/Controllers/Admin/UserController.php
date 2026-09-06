<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $search = request('search');

        $users = User::query()
            ->when($search, function ($query, $searchTerm) {
                $query->where(function ($nestedQuery) use ($searchTerm) {
                    $nestedQuery->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $oldValues = $user->only(['name', 'email', 'role']);
        $user->update($validated);

        ActivityLogService::log(
            'updated',
            $user,
            $oldValues,
            $user->only(['name', 'email', 'role']),
            'User updated by admin'
        );

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account from the admin panel.');
        }

        $oldValues = $user->only(['name', 'email', 'role']);
        $user->delete();

        ActivityLogService::log(
            'deleted',
            null,
            $oldValues,
            null,
            'User deleted by admin'
        );

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
