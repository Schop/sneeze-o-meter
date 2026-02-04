<x-guest-layout>
    <x-slot name="title">{{ __('auth.log_in') }}</x-slot>
    
    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('auth.email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('auth.password_field')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-3">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label" for="remember_me">
                {{ __('auth.remember_me') }}
            </label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
                <a class="btn btn-link btn-sm" href="{{ route('password.request') }}">
                    {{ __('auth.forgot_password') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('auth.log_in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-3 text-center">
        <p class="mb-0">{{ __('auth.already_registered') }}
            <a href="{{ route('register') }}" class="text-decoration-none">{{ __('auth.register') }}</a>
        </p>
    </div>
</x-guest-layout>
