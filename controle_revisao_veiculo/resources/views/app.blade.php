<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>@yield('title', 'Minha App')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite('resources/js/app.js')
</head>
<body>
    @inertia
</body>
</html>
