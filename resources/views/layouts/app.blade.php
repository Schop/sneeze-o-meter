<!DOCTYPE html>
<html lang="{{ app()->getLocale() == 'nl' ? 'nl-NL' : 'en-GB' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <div class="min-vh-100">
            <div class="container my-4">
                <div class="bg-white rounded-4 shadow-lg p-2 p-sm-3 p-md-4 pb-2">
                    <!-- Language Switcher -->
                    <div class="d-flex justify-content-end mb-2">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Language switcher">
                            <a href="{{ route('language.switch', 'en') }}" 
                               class="btn {{ app()->getLocale() == 'en' ? 'btn-primary' : 'btn-outline-secondary' }} d-flex align-items-center gap-1" 
                               title="English">
                                <span class="fi fi-gb" style="width: 1.2em; height: 1.2em; border-radius: 2px;"></span>
                                <span class="d-none d-sm-inline">EN</span>
                            </a>
                            <a href="{{ route('language.switch', 'nl') }}" 
                               class="btn {{ app()->getLocale() == 'nl' ? 'btn-primary' : 'btn-outline-secondary' }} d-flex align-items-center gap-1" 
                               title="Nederlands">
                                <span class="fi fi-nl" style="width: 1.2em; height: 1.2em; border-radius: 2px;"></span>
                                <span class="d-none d-sm-inline">NL</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Header with logo and title -->
                    <div class="row align-items-center mb-4 g-3">
                        <div class="col-auto px-2 px-md-5">
                            <a href="{{ route('home') }}">
                                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="img-fluid" style="height: clamp(80px, 15vw, 180px); cursor: pointer;">
                            </a>
                        </div>
                        <div class="col px-2 px-md-5">
                            <h1 style="font-size: clamp(1.5rem, 5vw, 4.5rem);">
                                <a href="{{ route('home') }}" class="text-decoration-none" style="cursor: pointer;"><b>{{ config('app.name') }}</b></a>
                            </h1>
                            <p class="text-muted mb-0 px-2 text-end" style="font-size: clamp(0.875rem, 2vw, 1.5rem);">{{ __('messages.general.tagline') }}</p>
                        </div>
                    </div>
                    
                    <!-- Navigation Menu -->
                    @include('layouts.navigation')
                    
                    <!-- Page Title for Mobile -->
                    @isset($title)
                        <div class="d-lg-none mt-3 mb-2">
                            <h2 class="h4 text-center text-primary mb-0">{{ $title }}</h2>
                        </div>
                    @endisset
                </div>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <div class="container my-4">
                    <div class="bg-white shadow rounded p-4">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <!-- Page Content -->
            <main class="container my-4">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="container mt-5 mb-4">
                <div class="bg-white rounded shadow p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="bi bi-envelope"></i> {{ __('messages.footer.contact') }}</h5>
                            <p class="text-muted mb-0">{{ __('messages.footer.questions') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-2">{{ __('messages.footer.email_us') }}:</p>
                            <a href="mailto:info@sneeze-o-meter.com" class="text-decoration-none fs-5">
                                <i class="bi bi-envelope-fill me-2"></i>info@sneeze-o-meter.com
                            </a>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="text-center text-muted small">
                        <p class="mb-0">
                            &copy; {{ date('Y') }} {{ config('app.name') }} - {{ __('messages.general.tagline') }}
                        </p>
                        @php
                            $latestMigration = \DB::table('migrations')->orderBy('batch', 'desc')->first();
                        @endphp
                        <p class="mb-0 mt-1">
                            {{ __('messages.footer.app_version') }}: {{ config('app.version') }}
                            @if($latestMigration)
                                | {{ __('messages.footer.db_version') }}: {{ $latestMigration->batch }}
                            @endif
                        </p>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Toast Container -->
        <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
            @if (session('success'))
            <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            @endif
            @if (session('error'))
            <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            @endif
            @if (session('warning'))
            <div id="warningToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            @endif
        </div>

        <!-- Record Sneeze Modal -->
        @auth
        <div class="modal fade" id="recordSneezeModal" tabindex="-1" aria-labelledby="recordSneezeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recordSneezeModalLabel"><i class="bi bi-plus-circle"></i> {{ __('messages.record.title') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('sneezes.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="sneeze_date" class="form-label">{{ __('messages.record.date') }}</label>
                                    <input type="date" 
                                           id="sneeze_date" 
                                           name="sneeze_date" 
                                           value="{{ old('sneeze_date', now()->format('Y-m-d')) }}"
                                           class="form-control">
                                    @error('sneeze_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="sneeze_time" class="form-label">{{ __('messages.record.time') }}</label>
                                    <input type="text" 
                                           id="sneeze_time" 
                                           name="sneeze_time" 
                                           value="{{ old('sneeze_time', now()->format('H:i')) }}"
                                           pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                           placeholder="HH:MM (e.g., 14:30)"
                                           class="form-control">
                                    @error('sneeze_time')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="count" class="form-label">{{ __('messages.record.number_of_sneezes') }}</label>
                                    <input type="number" 
                                           id="count" 
                                           name="count" 
                                           value="{{ old('count', 1) }}"
                                           min="1"
                                           max="100"
                                           class="form-control">
                                    @error('count')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label for="location" class="form-label">
                                        {{ __('messages.record.location') }}
                                    </label>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        @if(is_array(auth()->user()->custom_locations) && count(auth()->user()->custom_locations) > 0)
                                            @foreach(auth()->user()->custom_locations as $customLocation)
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation('{{ $customLocation }}')">{{ $customLocation }}</button>
                                            @endforeach
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation('{{ __('messages.locations.at_home') }}')">{{ __('messages.locations.at_home') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation('{{ __('messages.locations.at_work') }}')">{{ __('messages.locations.at_work') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation('{{ __('messages.locations.in_the_car') }}')">{{ __('messages.locations.in_the_car') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation('{{ __('messages.locations.outside') }}')">{{ __('messages.locations.outside') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setLocation('{{ __('messages.locations.inside_not_at_home') }}')">{{ __('messages.locations.inside_not_at_home') }}</button>
                                        @endif
                                    </div>
                                    <input type="text" 
                                           id="location" 
                                           name="location" 
                                           value="{{ old('location') }}"
                                           placeholder="{{ __('messages.record.or_type_location') }}"
                                           class="form-control">
                                    @error('location')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if(auth()->user()->use_precise_location)
                            <div class="row g-3 mb-3" id="coordinatesRow">
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label">{{ __('messages.record.latitude') }}</label>
                                    <input type="number" 
                                           id="latitude" 
                                           name="latitude" 
                                           value="{{ old('latitude') }}"
                                           step="0.00000001"
                                           min="-90"
                                           max="90"
                                           placeholder="e.g., 52.3676"
                                           class="form-control">
                                    @error('latitude')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="longitude" class="form-label">{{ __('messages.record.longitude') }}</label>
                                    <input type="number" 
                                           id="longitude" 
                                           name="longitude" 
                                           value="{{ old('longitude') }}"
                                           step="0.00000001"
                                           min="-180"
                                           max="180"
                                           placeholder="e.g., 4.9041"
                                           class="form-control">
                                    @error('longitude')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ __('messages.record.notes') }}</label>
                                <textarea id="notes" 
                                          name="notes" 
                                          rows="2" 
                                          class="form-control"
                                          placeholder="{{ __('messages.record.notes_placeholder') }}">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.record.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> {{ __('messages.record.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Sneeze Modal -->
        <div class="modal fade" id="editSneezeModal" tabindex="-1" aria-labelledby="editSneezeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSneezeModalLabel"><i class="bi bi-pencil-square"></i> {{ __('messages.record.edit_title') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editSneezeForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="edit_sneeze_date" class="form-label">{{ __('messages.record.date') }}</label>
                                    <input type="date" 
                                           id="edit_sneeze_date" 
                                           name="sneeze_date" 
                                           class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_sneeze_time" class="form-label">{{ __('messages.record.time') }}</label>
                                    <input type="time" 
                                           id="edit_sneeze_time" 
                                           name="sneeze_time" 
                                           pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                           placeholder="HH:MM (e.g., 14:30)"
                                           class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label for="edit_count" class="form-label">{{ __('messages.record.number_of_sneezes') }}</label>
                                    <input type="number" 
                                           id="edit_count" 
                                           name="count" 
                                           min="1"
                                           max="100"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-12">
                                    <label for="edit_location" class="form-label">{{ __('messages.record.location') }}</label>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        @if(is_array(auth()->user()->custom_locations) && count(auth()->user()->custom_locations) > 0)
                                            @foreach(auth()->user()->custom_locations as $customLocation)
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setEditLocation('{{ $customLocation }}')">{{ $customLocation }}</button>
                                            @endforeach
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setEditLocation('{{ __('messages.locations.at_home') }}')">{{ __('messages.locations.at_home') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setEditLocation('{{ __('messages.locations.at_work') }}')">{{ __('messages.locations.at_work') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setEditLocation('{{ __('messages.locations.in_the_car') }}')">{{ __('messages.locations.in_the_car') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setEditLocation('{{ __('messages.locations.outside') }}')">{{ __('messages.locations.outside') }}</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setEditLocation('{{ __('messages.locations.inside_not_at_home') }}')">{{ __('messages.locations.inside_not_at_home') }}</button>
                                        @endif
                                    </div>
                                    <input type="text" 
                                           id="edit_location" 
                                           name="location" 
                                           placeholder="{{ __('messages.record.or_type_location') }}"
                                           class="form-control">
                                </div>
                            </div>

                            @if(auth()->user()->use_precise_location)
                            <div class="row g-3 mb-3" id="editCoordinatesRow">
                                <div class="col-md-6">
                                    <label for="edit_latitude" class="form-label">{{ __('messages.record.latitude') }}</label>
                                    <input type="number" 
                                           id="edit_latitude" 
                                           name="latitude" 
                                           step="0.00000001"
                                           min="-90"
                                           max="90"
                                           placeholder="e.g., 52.3676"
                                           class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_longitude" class="form-label">{{ __('messages.record.longitude') }}</label>
                                    <input type="number" 
                                           id="edit_longitude" 
                                           name="longitude" 
                                           step="0.00000001"
                                           min="-180"
                                           max="180"
                                           placeholder="e.g., 4.9041"
                                           class="form-control">
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label for="edit_notes" class="form-label">{{ __('messages.record.notes') }}</label>
                                <textarea id="edit_notes" 
                                          name="notes" 
                                          rows="2" 
                                          class="form-control"
                                          placeholder="{{ __('messages.record.notes_placeholder') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.record.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> {{ __('messages.record.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endauth

        <!-- DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

        <script>
            // Function to set location for record modal
            function setLocation(location) {
                document.getElementById('location').value = location;
            }
            
            // Function to set location for edit modal
            function setEditLocation(location) {
                document.getElementById('edit_location').value = location;
            }
            
            // Handle edit modal population
            $(document).ready(function() {
                // Set current local date and time when record modal opens
                $('#recordSneezeModal').on('show.bs.modal', function (event) {
                    const now = new Date();
                    const dateStr = now.getFullYear() + '-' + 
                                   String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                                   String(now.getDate()).padStart(2, '0');
                    const timeStr = String(now.getHours()).padStart(2, '0') + ':' + 
                                   String(now.getMinutes()).padStart(2, '0');
                    
                    $('#sneeze_date').val(dateStr);
                    $('#sneeze_time').val(timeStr);
                });
                
                // Auto-detect coordinates when modal opens
                $('#recordSneezeModal').on('shown.bs.modal', function () {
                    @if(auth()->check() && auth()->user()->use_precise_location)
                    const latitudeInput = document.getElementById('latitude');
                    const longitudeInput = document.getElementById('longitude');
                    
                    // Only auto-fill if fields are empty
                    if (latitudeInput && longitudeInput && !latitudeInput.value && !longitudeInput.value) {
                        if ('geolocation' in navigator) {
                            navigator.geolocation.getCurrentPosition(
                                function(position) {
                                    const lat = position.coords.latitude;
                                    const lng = position.coords.longitude;
                                    
                                    // Fill in coordinates
                                    latitudeInput.value = lat.toFixed(8);
                                    longitudeInput.value = lng.toFixed(8);
                                },
                                function(error) {
                                    console.error('Geolocation error:', error);
                                },
                                {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 0
                                }
                            );
                        }
                    }
                    @endif
                });

                $('#editSneezeModal').on('show.bs.modal', function (event) {
                    const button = $(event.relatedTarget);
                    const id = button.data('id');
                    const date = button.data('date');
                    const time = button.data('time');
                    const location = button.data('location');
                    const latitude = button.data('latitude');
                    const longitude = button.data('longitude');
                    const count = button.data('count');
                    const notes = button.data('notes');

                    const form = $('#editSneezeForm');
                    form.attr('action', '/sneezes/' + id);
                    
                    $('#edit_sneeze_date').val(date);
                    $('#edit_sneeze_time').val(time);
                    $('#edit_count').val(count);
                    $('#edit_location').val(location || '');
                    $('#edit_latitude').val(latitude || '');
                    $('#edit_longitude').val(longitude || '');
                    $('#edit_notes').val(notes || '');
                });
            });
        </script>

        <script>
            // Initialize toasts
            document.addEventListener('DOMContentLoaded', function() {
                var toastElements = document.querySelectorAll('.toast');
                toastElements.forEach(function(toastElement) {
                    var toast = new bootstrap.Toast(toastElement, {
                        autohide: true,
                        delay: 5000
                    });
                    toast.show();
                });
            });
        </script>

        @stack('scripts')
    </body>
</html>
