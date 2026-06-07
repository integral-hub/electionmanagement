<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

<div class="flex">

    <aside class="w-64 bg-white h-screen p-4 border-r">
        @include('components.nav.sidebar')
    </aside>

    <main class="flex-1 p-6">
        {{ $slot }}
    </main>

</div>

</body>
</html>