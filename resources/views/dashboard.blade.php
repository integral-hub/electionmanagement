<x-app-layout>

    <h1 class="text-2xl font-bold mb-4">
        Dashboard
    </h1>

    <x-ui.card>
        Welcome back, {{ auth()->user()->name }}
    </x-ui.card>

</x-app-layout>