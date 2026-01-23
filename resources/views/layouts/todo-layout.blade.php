
//ovo nista ne radi

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>To-Do App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    @include('partials.navigation')

    <!-- Content -->
    <main class="grow">
        @yield('content')
    </main>

    @include('partials.footer')

</body>
</html>
