<button
    {{ $attributes->merge([
        'class' => 'w-full bg-black text-white py-2 rounded-lg hover:bg-gray-800'
    ]) }}
>
    {{ $slot }}
</button>