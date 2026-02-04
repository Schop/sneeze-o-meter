<section>
    <header>
        <h2 class="h5">
            {{ __('messages.profile.profile_information') }}
        </h2>

        <p class="text-muted mt-2">
            {{ __('messages.profile.profile_information_desc') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <x-input-label for="name" :value="__('messages.profile.name')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('messages.profile.email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-muted small mt-2">
                        {{ __('messages.profile.email_unverified') }}

                        <button form="send-verification" class="btn btn-link btn-sm p-0">
                            {{ __('messages.profile.resend_verification') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success small mt-2">
                            {{ __('messages.profile.verification_sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="mb-3">
            <x-input-label for="gender" :value="__('messages.profile.gender')" />
            <select id="gender" name="gender" class="form-select">
                <option value="">{{ __('messages.profile.gender_prefer_not_to_say') }}</option>
                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('messages.profile.gender_male') }}</option>
                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('messages.profile.gender_female') }}</option>
                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ __('messages.profile.gender_other') }}</option>
            </select>
            <div class="form-text">
                {{ __('messages.profile.gender_help') }}
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('gender')" />
        </div>

        <div class="mb-3">
            <x-input-label for="custom_locations" :value="__('messages.profile.custom_locations')" />
            <x-text-input id="custom_locations" name="custom_locations" type="text" :value="old('custom_locations', is_array($user->custom_locations) ? implode(', ', $user->custom_locations) : '')" autocomplete="off" />
            <div class="form-text">
                {{ __('messages.profile.custom_locations_help') }}
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('custom_locations')" />
        </div>

        <div class="mb-3">
            <x-input-label for="profile_picture" :value="__('messages.profile.profile_picture')" />
            <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/*">
            <div class="form-text">
                {{ __('messages.profile.profile_picture_help') }}
            </div>
            <div class="mt-2">
                <img src="{{ $user->profile_picture_url }}" alt="Profile Picture" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
            </div>
            <x-input-error class="mt-1" :messages="$errors->get('profile_picture')" />
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="use_precise_location" name="use_precise_location" value="1" {{ old('use_precise_location', $user->use_precise_location) ? 'checked' : '' }}>
                <label class="form-check-label" for="use_precise_location">
                    {{ __('messages.profile.use_precise_location') }}
                </label>
                <div class="form-text">
                    {{ __('messages.profile.use_precise_location_help') }}
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="show_in_leaderboard" name="show_in_leaderboard" value="1" {{ old('show_in_leaderboard', $user->show_in_leaderboard) ? 'checked' : '' }}>
                <label class="form-check-label" for="show_in_leaderboard">
                    {{ __('messages.profile.show_in_leaderboard') }}
                </label>
                <div class="form-text">
                    {{ __('messages.profile.show_in_leaderboard_help') }}
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-primary-button>{{ __('messages.profile.save') }}</x-primary-button>
        </div>
    </form>
</section>
