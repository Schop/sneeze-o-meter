<x-guest-layout>
    <x-slot name="title">{{ __('auth.forgot_password_title') }}</x-slot>
    
    <div class="mb-3 text-muted">
        {{ __('auth.forgot_password_text') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="d-flex justify-content-end">
            <x-primary-button>
                {{ __('auth.email_password_reset_link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
