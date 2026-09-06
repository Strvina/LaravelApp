@extends('layouts.nav-layout')

@section('content')
    <section class="page-shell">
        <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="panel bg-slate-900 text-white">
                <p class="stat-label text-amber-300">Contact</p>
                <h1 class="mt-3 text-4xl font-bold">Let’s talk about the project.</h1>
                <p class="mt-5 text-base leading-7 text-slate-300">
                    This page demonstrates server-side validation, Blade forms, and mail handling through a clean
                    contact workflow. Use it as a project inquiry or feedback channel.
                </p>

                <div class="mt-8 space-y-4 text-sm text-slate-300">
                    <p>Feature shown: validated form submission with Laravel mail.</p>
                    <p>Portfolio angle: practical business communication flow inside the app.</p>
                </div>
            </div>

            <div class="panel">
                <h2 class="section-title">Send a message</h2>
                <p class="mt-2 text-sm text-slate-600">Questions, feedback, or portfolio inquiries are all welcome.</p>

                <form action="{{ route('contact.send') }}" method="POST" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="field-label">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="text-input @error('name') border-rose-300 ring-rose-100 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="field-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="text-input @error('email') border-rose-300 ring-rose-100 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="field-label">Message</label>
                        <textarea name="message" id="message" rows="6" required
                            class="text-input @error('message') border-rose-300 ring-rose-100 @enderror">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="primary-btn">Send message</button>
                </form>
            </div>
        </div>
    </section>
@endsection
