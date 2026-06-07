@props(['name', 'type' => 'text', 'placeholder' => ''])

<input
    name="{{ $name }}"
    type="{{ $type }}"
    placeholder="{{ $placeholder }}"
    value="{{ old($name) }}"
    class="w-full border rounded-lg px-3 py-2 focus:ring"
/>

@error($name)
    <p class="text-red-500 text-xs mt-1">
        {{ $message }}
    </p>
@enderror