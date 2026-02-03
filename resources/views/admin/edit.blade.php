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
                        <form method="POST" action="{{ route('admin.updateUser', $user) }}">
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
