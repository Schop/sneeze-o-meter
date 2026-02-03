<x-guest-layout>
    <div class="mb-3 text-muted">
        {{ __('auth.secure_area_text') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="d-flex justify-content-end">
            <x-primary-button>
                {{ __('auth.confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
