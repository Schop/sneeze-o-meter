<section>
    <header>
        <h2 class="h5">
            {{ __('messages.profile.delete_account') }}
        </h2>

        <p class="text-muted mt-2">
            {{ __('messages.profile.delete_account_desc') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="mt-3"
    >{{ __('messages.profile.delete_account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
            @csrf
            @method('delete')

            <h2 class="h5">
                {{ __('messages.profile.delete_confirm_title') }}
            </h2>

            <p class="text-muted mt-2">
                {{ __('messages.profile.delete_confirm_desc') }}
            </p>

            <div class="mt-3">
                <x-input-label for="password" :value="__('messages.profile.password')" class="visually-hidden" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    :placeholder="__('messages.profile.password')"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>

            <div class="mt-3 d-flex justify-content-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('messages.profile.cancel') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('messages.profile.delete_account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
