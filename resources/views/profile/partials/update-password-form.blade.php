<section>
    <header>
        <h2 class="h5">
            {{ __('messages.profile.update_password') }}
        </h2>

        <p class="text-muted mt-2">
            {{ __('messages.profile.update_password_desc') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <x-input-label for="update_password_current_password" :value="__('messages.profile.current_password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password" :value="__('messages.profile.new_password')" />
            <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password_confirmation" :value="__('messages.profile.confirm_password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-primary-button>{{ __('messages.profile.save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-muted small mb-0"
                >{{ __('messages.profile.saved') }}</p>
            @endif
        </div>
    </form>
</section>
