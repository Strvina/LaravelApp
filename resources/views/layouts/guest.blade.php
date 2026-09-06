<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'OpsDesk') }}</title>

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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|space-grotesk:500,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-shell flex min-h-screen flex-col items-center justify-center bg-[radial-gradient(circle_at_top,_rgba(240,146,53,0.14),_transparent_35%),linear-gradient(180deg,_#fffdf8_0%,_#fff8ee_100%)] px-4 py-10 text-slate-900">
        <a href="/" class="mb-6 flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 text-sm font-bold text-white">OD</span>
            <div>
                <p class="text-lg font-bold text-slate-900">OpsDesk</p>
                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Portfolio Workspace</p>
            </div>
        </a>

        <div class="panel w-full max-w-md">
            {{ $slot }}
        </div>
    </body>
</html>
