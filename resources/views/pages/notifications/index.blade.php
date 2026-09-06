@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Workspace</p>
                <h1 class="page-title">Notifications</h1>
                <p class="page-subtitle">Review stock alerts, system messages, and task reminders in one place.</p>
            </div>

            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="secondary-btn">Mark all read</button>
            </form>
        </div>

        <div class="panel">
            <div class="space-y-3">
                @forelse ($notifications as $notification)
                    <div class="rounded-lg border {{ $notification->read_at ? 'border-slate-200 bg-white' : 'border-amber-200 bg-amber-50' }} px-4 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">{{ str_replace('_', ' ', $notification->type) }}</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ $notification->message }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>

                            @unless ($notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="secondary-btn">Mark read</button>
                                </form>
                            @endunless
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-200 bg-white p-6 text-sm text-slate-500">
                        No notifications yet.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        </div>
    </section>
@endsection
