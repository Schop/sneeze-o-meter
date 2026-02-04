<x-app-layout>
    <x-slot name="header">
        <h2 class="font-weight-bold h4 text-dark mb-0">
            {{ __('messages.admin.edit_user') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('messages.admin.edit_user') }}: {{ $user->name }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.updateUser', $user) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('messages.admin.name') }}</label>
                                <input id="name" 
                                       type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required 
                                       autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('messages.admin.email') }}</label>
                                <input id="email" 
                                       type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Admin Status -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_admin" 
                                           name="is_admin" 
                                           value="1"
                                           {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                           @if($user->id === auth()->id() && $user->is_admin) disabled @endif>
                                    <label class="form-check-label" for="is_admin">
                                        {{ __('messages.admin.is_admin') }}
                                    </label>
                                    @if($user->id === auth()->id() && $user->is_admin)
                                        <small class="text-muted d-block">{{ __('messages.admin.cannot_remove_own_admin') }}</small>
                                    @endif
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="mb-3">
                                <label for="gender" class="form-label">{{ __('messages.profile.gender') }}</label>
                                <select id="gender" 
                                        class="form-select @error('gender') is-invalid @enderror" 
                                        name="gender">
                                    <option value="">{{ __('messages.profile.gender_prefer_not_to_say') }}</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('messages.profile.gender_male') }}</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('messages.profile.gender_female') }}</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ __('messages.profile.gender_other') }}</option>
                                </select>
                                <div class="form-text">{{ __('messages.profile.gender_help') }}</div>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Custom Locations -->
                            <div class="mb-3">
                                <label for="custom_locations" class="form-label">{{ __('messages.profile.custom_locations') }}</label>
                                <input id="custom_locations" 
                                       type="text" 
                                       class="form-control @error('custom_locations') is-invalid @enderror" 
                                       name="custom_locations" 
                                       value="{{ old('custom_locations', is_array($user->custom_locations) ? implode(', ', $user->custom_locations) : '') }}">
                                <div class="form-text">{{ __('messages.profile.custom_locations_help') }}</div>
                                @error('custom_locations')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Profile Picture -->
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">{{ __('messages.profile.profile_picture') }}</label>
                                <input id="profile_picture" 
                                       type="file" 
                                       class="form-control @error('profile_picture') is-invalid @enderror" 
                                       name="profile_picture" 
                                       accept="image/*">
                                <div class="form-text">{{ __('messages.profile.profile_picture_help') }}</div>
                                @if($user->profile_picture)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Current Profile Picture" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                    </div>
                                @endif
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Use Precise Location -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="use_precise_location" 
                                           name="use_precise_location" 
                                           value="1"
                                           {{ old('use_precise_location', $user->use_precise_location) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="use_precise_location">
                                        {{ __('messages.profile.use_precise_location') }}
                                    </label>
                                    <div class="form-text">{{ __('messages.profile.use_precise_location_help') }}</div>
                                </div>
                            </div>

                            <!-- Show in Leaderboard -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="show_in_leaderboard" 
                                           name="show_in_leaderboard" 
                                           value="1"
                                           {{ old('show_in_leaderboard', $user->show_in_leaderboard) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_in_leaderboard">
                                        {{ __('messages.profile.show_in_leaderboard') }}
                                    </label>
                                    <div class="form-text">{{ __('messages.profile.show_in_leaderboard_help') }}</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                                    {{ __('messages.admin.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('messages.admin.save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
