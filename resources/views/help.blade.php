<x-app-layout>
    <x-slot name="title">{{ __('messages.help.title') }}</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="bg-white rounded-4 shadow-lg p-4 mb-4">
                <h1 class="h2 mb-4">
                            <i class="bi bi-question-circle"></i> {{ __('messages.help.title') }}
                        </h1>

                        <!-- About Section -->
                        <section class="mb-5">
                            <h2 class="h4 mb-3">{{ __('messages.help.about_title') }}</h2>
                            <p class="lead">{{ __('messages.help.about_text') }}</p>
                        </section>

                        <!-- Quick Sneeze Section -->
                        <section class="mb-5">
                            <h2 class="h4 mb-3">
                                <i class="bi bi-lightning"></i> {{ __('messages.help.quick_sneeze_title') }}
                            </h2>
                            <p>{{ __('messages.help.quick_sneeze_intro') }}</p>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>{{ __('messages.help.quick_link') }}:</strong>
                                <a href="{{ route('quick.sneeze') }}" class="alert-link">{{ url('/quick-sneeze') }}</a>
                            </div>

                            <div class="row g-3 mt-2 mb-4">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h3 class="h5 mb-3">
                                                <i class="bi bi-apple"></i> {{ __('messages.help.ios_instructions_title') }}
                                            </h3>
                                            <ol class="mb-0">
                                                <li>{{ __('messages.help.ios_step_1') }}</li>
                                                <li>{{ __('messages.help.ios_step_2') }}</li>
                                                <li>{{ __('messages.help.ios_step_3') }}</li>
                                                <li>{{ __('messages.help.ios_step_4') }}</li>
                                                <li>{{ __('messages.help.ios_step_5') }}</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h3 class="h5 mb-3">
                                                <i class="bi bi-android2"></i> {{ __('messages.help.android_instructions_title') }}
                                            </h3>
                                            <ol class="mb-0">
                                                <li>{{ __('messages.help.android_step_1') }}</li>
                                                <li>{{ __('messages.help.android_step_2') }}</li>
                                                <li>{{ __('messages.help.android_step_3') }}</li>
                                                <li>{{ __('messages.help.android_step_4') }}</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i>
                                {{ __('messages.help.shortcut_tip') }}
                            </div>
                        </section>

                        <!-- FAQ Section -->
                        <section class="mb-5">
                            <h2 class="h4 mb-3">{{ __('messages.help.faq_title') }}</h2>
                            
                            <div class="accordion" id="faqAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            {{ __('messages.help.faq_1_q') }}
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('messages.help.faq_1_a') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            {{ __('messages.help.faq_2_q') }}
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('messages.help.faq_2_a') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            {{ __('messages.help.faq_3_q') }}
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('messages.help.faq_3_a') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                            {{ __('messages.help.faq_4_q') }}
                                        </button>
                                    </h2>
                                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('messages.help.faq_4_a') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                            {{ __('messages.help.faq_5_q') }}
                                        </button>
                                    </h2>
                                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {{ __('messages.help.faq_5_a') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Features Section -->
                        <section class="mb-5">
                            <h2 class="h4 mb-3">{{ __('messages.help.features_title') }}</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bi bi-graph-up text-primary fs-4 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">{{ __('messages.help.feature_tracking') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.help.feature_tracking_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bi bi-geo-alt text-primary fs-4 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">{{ __('messages.help.feature_location') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.help.feature_location_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bi bi-bar-chart text-primary fs-4 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">{{ __('messages.help.feature_stats') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.help.feature_stats_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bi bi-download text-primary fs-4 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">{{ __('messages.help.feature_export') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.help.feature_export_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bi bi-sliders text-primary fs-4 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">{{ __('messages.help.feature_custom') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.help.feature_custom_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <i class="bi bi-translate text-primary fs-4 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">{{ __('messages.help.feature_multilingual') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.help.feature_multilingual_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Contact/Support Section -->
                        <section>
                            <h2 class="h4 mb-3">{{ __('messages.help.support_title') }}</h2>
                            <p>{{ __('messages.help.support_text') }}</p>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
