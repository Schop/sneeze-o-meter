<x-guest-layout>
    <x-slot name="title">{{ __('auth.register') }}</x-slot>
    
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <h4 class="text-center">{{ __('auth.register_account') }}</h4>
        </div>
        <!-- Name -->
        <div class="mb-3">
            <x-input-label for="name" :value="__('auth.name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('auth.email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
             <div class="form-text">
                    {{ __('auth.email_help') }}
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('auth.password_field')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <x-input-label for="password_confirmation" :value="__('auth.confirm_password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <!-- Location Tracking -->
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="use_precise_location" name="use_precise_location" value="1" checked>
                <label class="form-check-label" for="use_precise_location">
                    {{ __('auth.use_precise_location') }}
                </label>
                <div class="form-text">
                    {{ __('auth.use_precise_location_help') }}
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="show_in_leaderboard" name="show_in_leaderboard" value="1" checked>
                <label class="form-check-label" for="show_in_leaderboard">
                    {{ __('auth.show_in_leaderboard') }}
                </label>
                <div class="form-text">
                    {{ __('auth.show_in_leaderboard_help') }}
                </div>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input @error('agree_terms') is-invalid @enderror" type="checkbox" id="agree_terms" name="agree_terms" value="1" {{ old('agree_terms') ? 'checked' : '' }} required>
                <label class="form-check-label" for="agree_terms">
                    {{ __('auth.agree_terms') }}
                </label>
                <div class="form-text">
                    {{ __('auth.agree_terms_help') }}
                    <a href="{{ route('terms') }}" target="_blank" class="link-primary ms-2">
                        {{ __('auth.read_terms') }} <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
                <x-input-error :messages="$errors->get('agree_terms')" class="mt-1" />
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a class="btn btn-link btn-sm" href="{{ route('login') }}">
                {{ __('auth.already_registered') }}
            </a>

            <x-primary-button>
                {{ __('auth.register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
