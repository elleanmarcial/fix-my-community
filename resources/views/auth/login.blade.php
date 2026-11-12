<x-guest-layout>
    <h2 style="margin-top:20px">Welcome back</h2>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="form" style="margin-top:16px">
        @csrf

        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="fmc-check">
            <input id="remember_me" type="checkbox" name="remember">
            <span>Keep me signed in</span>
        </label>

        <x-primary-button>Sign in</x-primary-button>

        @if (Route::has('password.request'))
            <a class="link" href="{{ route('password.request') }}">Forgot your password?</a>
        @endif
    </form>
</x-guest-layout>
