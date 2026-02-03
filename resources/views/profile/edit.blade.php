<x-app-layout>
    <x-slot name="title">{{ __('messages.profile.title') }}</x-slot>
    
    <div class="py-4">
        <div class="container">
            <div class="mb-4">
                <h2 class="fw-bold text-primary">{{ __('messages.profile.title') }}</h2>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <section>
                                <header>
                                    <h2 class="h5">
                                        <i class="bi bi-download"></i> {{ __('messages.profile.download_data') }}
                                    </h2>
                                    <p class="text-muted mt-2">
                                        {{ __('messages.profile.download_data_desc') }}
                                    </p>
                                </header>
                                <div class="mt-3">
                                    <a href="{{ route('sneezes.export') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-file-earmark-arrow-down"></i> {{ __('messages.profile.download_csv') }}
                                    </a>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
