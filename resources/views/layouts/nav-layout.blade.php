<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'OpsDesk') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (() => {
            const savedTheme = localStorage.getItem('opsdesk-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const useDark = savedTheme ? savedTheme === 'dark' : prefersDark;

            if (useDark) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|space-grotesk:500,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell flex min-h-screen flex-col bg-[radial-gradient(circle_at_top,_rgba(240,146,53,0.14),_transparent_35%),linear-gradient(180deg,_#fffdf8_0%,_#fff8ee_100%)] text-slate-900">
    @include('partials.navigation')

    <main class="flex-1 pb-12">
        @if (session('success'))
            <div class="mx-auto mt-6 max-w-6xl px-4">
                <div class="flash-success rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto mt-6 max-w-6xl px-4">
                <div class="flash-error rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
