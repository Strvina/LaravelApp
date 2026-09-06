@extends('layouts.nav-layout')

@section('title', 'Profile')

@section('content')
    <section class="page-shell">
        <div class="page-header">
            <div>
                <p class="stat-label">Account module</p>
                <h1 class="page-title">Profile settings</h1>
                <p class="page-subtitle">Review your account details, update credentials, and manage account security.</p>
            </div>
        </div>

        <div class="grid gap-8 xl:grid-cols-[0.75fr_1.25fr]">
            <div class="panel">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-2xl font-bold text-amber-800">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="section-title">{{ auth()->user()->name }}</h2>
                        <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4">
                    <div class="rounded-2xl border border-slate-200 px-4 py-4">
                        <p class="stat-label">Role</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 px-4 py-4">
                        <p class="stat-label">Member since</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 px-4 py-4">
                        <p class="stat-label">Verification</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">
                            {{ auth()->user()->email_verified_at ? 'Verified email' : 'Pending verification' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="panel">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="panel">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="panel">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
