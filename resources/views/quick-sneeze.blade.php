<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.quick.title') }} - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center p-3">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="width: 100px; height: 100px;">
            <h1 class="h3 mt-3">{{ __('messages.quick.title') }}</h1>
        </div>

        <div class="card shadow" style="width: 100%; max-width: 500px;">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('sneezes.store') }}">
                    @csrf
                    <input type="hidden" name="redirect_to" value="dashboard">

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="sneeze_date" class="form-label">{{ __('messages.record.date') }}</label>
                            <input type="date" 
                                   id="sneeze_date" 
                                   name="sneeze_date" 
                                   value="{{ old('sneeze_date', now()->format('Y-m-d')) }}"
                                   class="form-control form-control-lg">
                        </div>
                        <div class="col-6">
                            <label for="sneeze_time" class="form-label">{{ __('messages.record.time') }}</label>
                            <input type="time" 
                                   id="sneeze_time" 
                                   name="sneeze_time" 
                                   value="{{ old('sneeze_time', now()->format('H:i')) }}"
                                   class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="count" class="form-label">{{ __('messages.record.number_of_sneezes') }}</label>
                        <input type="number" 
                               id="count" 
                               name="count" 
                               value="{{ old('count', 1) }}"
                               min="1"
                               max="100"
                               class="form-control form-control-lg">
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">{{ __('messages.record.location') }}</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @if(is_array(auth()->user()->custom_locations) && count(auth()->user()->custom_locations) > 0)
                                @foreach(auth()->user()->custom_locations as $customLocation)
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('location').value='{{ $customLocation }}'">{{ $customLocation }}</button>
                                @endforeach
                            @else
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('location').value='{{ __('messages.locations.at_home') }}'">{{ __('messages.locations.at_home') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('location').value='{{ __('messages.locations.at_work') }}'">{{ __('messages.locations.at_work') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('location').value='{{ __('messages.locations.in_the_car') }}'">{{ __('messages.locations.in_the_car') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('location').value='{{ __('messages.locations.outside') }}'">{{ __('messages.locations.outside') }}</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('location').value='{{ __('messages.locations.inside_not_at_home') }}'">{{ __('messages.locations.inside_not_at_home') }}</button>
                            @endif
                        </div>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               value="{{ old('location') }}"
                               placeholder="{{ __('messages.record.or_type_location') }}"
                               class="form-control form-control-lg">
                    </div>

                    @if(auth()->user()->use_precise_location)
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" 
                                       id="latitude" 
                                       name="latitude" 
                                       value="{{ old('latitude') }}"
                                       step="0.00000001"
                                       placeholder="{{ __('messages.record.latitude') }}"
                                       class="form-control"
                                       readonly>
                            </div>
                            <div class="col-6">
                                <input type="number" 
                                       id="longitude" 
                                       name="longitude" 
                                       value="{{ old('longitude') }}"
                                       step="0.00000001"
                                       placeholder="{{ __('messages.record.longitude') }}"
                                       class="form-control"
                                       readonly>
                            </div>
                        </div>
                        <small id="location-status" class="text-muted"></small>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> {{ __('messages.quick.log_sneeze') }}
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            {{ __('messages.quick.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <small class="text-muted">{{ __('messages.quick.tip') }}</small>
        </div>
    </div>

    @if(auth()->user()->use_precise_location)
    <script>
        // Automatically detect location on page load
        document.addEventListener('DOMContentLoaded', function() {
            const statusElement = document.getElementById('location-status');
            
            if (!navigator.geolocation) {
                statusElement.textContent = '{{ __('messages.record.geolocation_not_supported') }}';
                statusElement.classList.add('text-danger');
                return;
            }

            statusElement.innerHTML = '<i class="bi bi-geo-alt"></i> {{ __('messages.record.detecting_location') }}';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    statusElement.innerHTML = '<i class="bi bi-check-circle text-success"></i> {{ __('messages.record.location_detected') }}';
                },
                function(error) {
                    statusElement.innerHTML = '<i class="bi bi-x-circle text-danger"></i> {{ __('messages.record.location_denied') }}';
                }
            );
        });
    </script>
    @endif
</body>
</html>
