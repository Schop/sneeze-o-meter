<x-app-layout>
    <x-slot name="title">{{ __('terms.title') }}</x-slot>
    
    <div class="py-4">
        <div class="container">
            <div class="mb-4">
                <h1 class="fw-bold text-primary">{{ __('terms.title') }}</h1>
            </div>

            <div class="row">
                <div class="col-12 col-lg-12 mx-auto">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <!-- Section 1 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-shield-lock text-primary"></i>
                                    {{ __('terms.section_1_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_1_text') }}
                                </p>
                            </div>

                            <!-- Section 2 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-database text-primary"></i>
                                    {{ __('terms.section_2_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_2_text') }}
                                </p>
                            </div>

                            <!-- Section 3 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-trash text-primary"></i>
                                    {{ __('terms.section_3_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_3_text') }}
                                </p>
                            </div>

                            <!-- Section 4 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    {{ __('terms.section_4_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_4_text') }}
                                </p>
                            </div>

                            <!-- Section 5 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-trophy text-primary"></i>
                                    {{ __('terms.section_5_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_5_text') }}
                                </p>
                            </div>

                            <!-- Section 6 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-lightning text-primary"></i>
                                    {{ __('terms.section_6_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_6_text') }}
                                </p>
                            </div>

                            <!-- Section 7 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-hand-thumbs-up text-primary"></i>
                                    {{ __('terms.section_7_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_7_text') }}
                                </p>
                            </div>

                            <!-- Section 8 -->
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-3">
                                    <i class="bi bi-pencil text-primary"></i>
                                    {{ __('terms.section_8_title') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('terms.section_8_text') }}
                                </p>
                            </div>

                            <hr class="my-5">

                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>{{ __('terms.title') }}</strong><br>
                                <small>Last updated: {{ date('F j, Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="bi bi-house"></i> {{ __('messages.nav.home') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
