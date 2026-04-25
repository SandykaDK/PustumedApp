<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'PustumedApp')</title>

    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/outline/index.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    @yield('css')
</head>
<body>
    @yield('content')
</body>
</html>
