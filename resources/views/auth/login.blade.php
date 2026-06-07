<x-guest-layout>

<div class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">

            {{-- Header --}}
            <div class="bg-black text-white p-6 text-center">
                <h1 class="text-xl font-semibold">
                    Welcome Back
                </h1>
                <p class="text-sm text-gray-300 mt-1">
                    Sign in to continue
                </p>
            </div>

            {{-- Form --}}
            <div class="p-6 space-y-5">

                {{-- Global Error --}}
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            required
                        >

                        @error('email')
                            <p class="text-sm text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="text-sm font-medium text-gray-600">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            required
                        >

                        @error('password')
                            <p class="text-sm text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between text-sm">

                        <label class="flex items-center gap-2 text-gray-600">
                            <input type="checkbox" name="remember" class="rounded">
                            Remember me
                        </label>

                        <a href="#" class="text-black hover:underline">
                            Forgot password?
                        </a>

                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full bg-black text-white py-2.5 rounded-lg hover:bg-gray-900 transition"
                    >
                        Sign in
                    </button>

                </form>

            </div>

        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-500 mt-4">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>

    </div>

</div>

</x-guest-layout>