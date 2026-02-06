<x-app-layout>
    <x-slot name="title">{{ __('messages.monthly.title') }} - {{ $date->translatedFormat('F Y') }}</x-slot>
    
    <div class="py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('monthly.details', ['month' => $date->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-chevron-left"></i> {{ __('messages.monthly.previous_month') }}
                            </a>
                            <div class="text-center flex-grow-1">
                                <h2 class="mb-1">
                                    {{ $date->translatedFormat('F Y') }}
                                </h2>
                            </div>
                            <div class="d-flex gap-2">
                                @if(!$date->isCurrentMonth())
                                <a href="{{ route('monthly.details', ['month' => $date->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-outline-secondary">
                                    {{ __('messages.monthly.next_month') }} <i class="bi bi-chevron-right"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User & General Stats Table Cards Row + Top Sneezers Leaderboard -->
        <div class="row mb-4 justify-content-center">
            <div class="col-12 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="text-muted mb-3"><i class="bi bi-person-circle"></i> {{ __('messages.monthly.your_stats') }}</h5>
                        @auth
                        @if($userStats && $userStats['total_count'] > 0)
                        <table class="compact-stats-table w-100">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-droplet-fill text-primary me-1"></i> {{ __('messages.monthly.your_sneezes') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($userStats['total_count']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-list-ol text-primary me-1"></i> {{ __('messages.monthly.your_events') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($userStats['total_events']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-calendar2-day text-primary me-1"></i> {{ __('messages.monthly.avg_per_day') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($userStats['total_count'] / $date->daysInMonth, 1) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted">{{ __('messages.monthly.no_data') }}</p>
                        @endif
                        @else
                        <p class="text-muted mb-3">{{ __('messages.monthly.login_to_view_personal') }}</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-box-arrow-in-right"></i> {{ __('messages.nav.login') }}
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-person-plus"></i> {{ __('messages.nav.register') }}
                            </a>
                        </div>
                        @endauth
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="text-muted mb-3"><i class="bi bi-clipboard-data"></i> {{ __('messages.monthly.general_stats') }}</h5>
                        <table class="compact-stats-table w-100">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-people-fill text-primary me-1"></i> {{ __('messages.monthly.active_users') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($generalStats['active_users']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-droplet-fill text-primary me-1"></i> {{ __('messages.monthly.total_sneezes') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($generalStats['total_sneezes']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-list-ol text-primary me-1"></i> {{ __('messages.monthly.total_events') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($generalStats['total_events']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-calendar2-day text-primary me-1"></i> {{ __('messages.monthly.avg_per_day') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($generalStats['total_sneezes'] / $date->daysInMonth, 1) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="text-muted mb-3"><i class="bi bi-trophy"></i> {{ __('messages.monthly.top_sneezers') }}</h5>
                        @php
                            $top5 = $topSneezers->take(5);
                            $userInTop5 = $top5->pluck('id')->contains(auth()->id());
                            $currentUser = null;
                            if(auth()->check() && !$userInTop5) {
                                $currentUser = $topSneezers->first(function($s) { return $s->id === auth()->id(); });
                            }
                        @endphp
                        @if($top5->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($top5 as $index => $sneezer)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center @if(auth()->check() && $sneezer->id === auth()->id()) bg-info bg-opacity-10 @endif">
                                        <span>
                                            <strong class="me-1">{{ $index + 1 }}.</strong>
                                            {{ \Illuminate\Support\Str::limit($sneezer->name, 15) }}
                                            @if(auth()->check() && $sneezer->id === auth()->id()) ({{ __('messages.general.you') }}) @endif
                                        </span>
                                        <span class="fw-bold">{{ $sneezer->sneeze_count }}</span>
                                    </div>
                                @endforeach
                                @if($currentUser)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center bg-info bg-opacity-10 border-top">
                                        <span>
                                            <strong>{{ $topSneezers->search($currentUser) + 1 }}.</strong> {{ \Illuminate\Support\Str::limit($currentUser->name, 15) }} ({{ __('messages.general.you') }})
                                        </span>
                                        <span class="fw-bold">{{ $currentUser->sneeze_count }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3 text-center">
                                <a href="{{ route('leaderboard') }}?type=monthly&month={{ $date->format('Y-m') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-trophy"></i> {{ __('messages.home.view_full_leaderboard') }}
                                </a>
                            </div>
                        @else
                            <p class="text-muted">{{ __('messages.monthly.no_data') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Heatmaps -->
        <div class="row g-4 mb-4">
            @auth
            @if(count($userHeatmapData) > 0)
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-1">
                            <i class="bi bi-geo-alt"></i>
                            {{ __('messages.monthly.your_heatmap') }}
                        </h4>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-lock-fill"></i> {{ __('messages.monthly.your_heatmap_privacy') }}
                        </p>
                        <div id="userHeatmap" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
            @endif
            @endauth
            @if(count($allHeatmapData) > 0)
            <div class="col-12 @auth @if(count($userHeatmapData) > 0) col-lg-6 @else col-lg-12 @endif @else col-lg-12 @endauth">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-1">
                            <i class="bi bi-geo-alt"></i>
                            {{ __('messages.monthly.all_users_heatmap') }}
                        </h4>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-shield-check"></i> {{ __('messages.monthly.all_users_heatmap_privacy') }}
                        </p>
                        <div id="allHeatmap" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Daily Distribution Charts -->
        <div class="row g-4 mb-4">
            @auth
            @if($userStats && $userStats['total_count'] > 0)
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="bi bi-graph-up"></i>
                            {{ __('messages.monthly.your_daily_distribution') }}
                        </h4>
                        <div style="height: 350px;">
                            <canvas id="userDailyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="bi bi-graph-up"></i>
                            {{ __('messages.monthly.your_daily_distribution') }}
                        </h4>
                        <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                            <div class="text-center">
                                <p class="text-muted mb-3">{{ __('messages.monthly.login_to_view_personal') }}</p>
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm me-2">
                                    {{ __('messages.nav.login') }}
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">
                                    {{ __('messages.nav.register') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @else
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="bi bi-graph-up"></i>
                            {{ __('messages.monthly.your_daily_distribution') }}
                        </h4>
                        <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                            <div class="text-center">
                                <p class="text-muted mb-3">{{ __('messages.monthly.login_to_view_personal') }}</p>
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm me-2">
                                    {{ __('messages.nav.login') }}
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">
                                    {{ __('messages.nav.register') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endauth
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="bi bi-graph-up"></i>
                            {{ __('messages.monthly.all_users_daily_distribution') }}
                        </h4>
                        <div style="height: 350px;">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @auth
        @if($userSneezes->count() > 0)
        <!-- User's Sneezes Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="bi bi-list-ul"></i>
                            {{ __('messages.monthly.your_sneezes_this_month') }}
                        </h4>
                        <div class="table-responsive">
                            <table id="userSneezesTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.dashboard.date') }}</th>
                                        <th>{{ __('messages.dashboard.time') }}</th>
                                        <th>{{ __('messages.dashboard.count') }}</th>
                                        <th>{{ __('messages.dashboard.location') }}</th>
                                        <th>{{ __('messages.dashboard.coordinates') }}</th>
                                        <th>{{ __('messages.dashboard.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userSneezes as $sneeze)
                                        <tr>
                                            <td>{{ \App\Helpers\DateHelper::formatLocalized(\Carbon\Carbon::parse($sneeze->sneeze_date)) }}</td>
                                            <td>{{ substr($sneeze->sneeze_time, 0, 5) }}</td>
                                            <td>{{ $sneeze->count }}</td>
                                            <td>{{ $sneeze->location ?: '-' }}</td>
                                            <td>
                                                @if($sneeze->latitude && $sneeze->longitude)
                                                    <a href="https://www.google.com/maps?q={{ $sneeze->latitude }},{{ $sneeze->longitude }}" 
                                                       target="_blank"
                                                       class="link-primary"
                                                       title="View on map">
                                                        {{ number_format($sneeze->latitude, 4) }}, {{ number_format($sneeze->longitude, 4) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $sneeze->notes ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endauth
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const primaryColor = '#1d4638';
        const dailyData = @json($dailyDataComplete);
        const userDailyData = @json($userDailyDataComplete);
        const userHeatmapData = @json($userHeatmapData ?? []);
        const allHeatmapData = @json($allHeatmapData ?? []);

        // Initialize user's personal heatmap (simpler style)
        function initHeatmap(elementId, data) {
            if (data.length === 0) return;

            // Calculate center based on data points
            const avgLat = data.reduce((sum, point) => sum + parseFloat(point.lat), 0) / data.length;
            const avgLng = data.reduce((sum, point) => sum + parseFloat(point.lng), 0) / data.length;

            const map = L.map(elementId).setView([avgLat, avgLng], 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            // Collect all coordinates for bounds
            const bounds = [];

            // Add markers for each sneeze location
            data.forEach(point => {
                const lat = parseFloat(point.lat);
                const lng = parseFloat(point.lng);
                const count = parseFloat(point.count);
                
                bounds.push([lat, lng]);
                
                L.circleMarker([lat, lng], {
                    radius: Math.min(5 + count * 2, 20),
                    fillColor: '#d9534f',
                    color: '#8b0000',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.7
                }).bindPopup(`${count} sneeze${count > 1 ? 's' : ''}`).addTo(map);
            });

            // Fit map to bounds with padding
            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }

            // Prepare heatmap data: [lat, lng, intensity]
            const heatData = data.map(point => [
                parseFloat(point.lat),
                parseFloat(point.lng),
                parseFloat(point.count) * 0.5
            ]);

            // Add heatmap layer
            L.heatLayer(heatData, {
                radius: 30,
                blur: 20,
                maxZoom: 13,
                max: 5,
                gradient: {
                    0.0: 'blue',
                    0.5: 'lime',
                    0.7: 'yellow',
                    1.0: 'red'
                }
            }).addTo(map);
        }

        // Initialize all users heatmap (matches home page style)
        function initAllUsersHeatmap(elementId, data) {
            if (data.length === 0) return;

            // Calculate center based on data points
            const avgLat = data.reduce((sum, point) => sum + parseFloat(point.lat), 0) / data.length;
            const avgLng = data.reduce((sum, point) => sum + parseFloat(point.lng), 0) / data.length;

            const map = L.map(elementId, {
                scrollWheelZoom: false,
                doubleClickZoom: false,
                touchZoom: false,
                zoomControl: false,
                dragging: true
            }).setView([avgLat, avgLng], 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            // Prepare data for heatmap - format: [lat, lng, intensity]
            const heatPoints = data.map(point => [
                parseFloat(point.lat),
                parseFloat(point.lng),
                parseFloat(point.count)
            ]);
            
            // Calculate bounds to fit all points
            const bounds = L.latLngBounds(data.map(p => [parseFloat(p.lat), parseFloat(p.lng)]));
            map.fitBounds(bounds, { padding: [50, 50] });
            
            // Add heatmap layer (matching home page style - heatmap only, no markers)
            L.heatLayer(heatPoints, {
                radius: 40,
                blur: 25,
                maxZoom: 17,
                max: 10,
                minOpacity: 0.5,
                gradient: {
                    0.0: 'blue',
                    0.3: 'cyan',
                    0.5: 'lime',
                    0.7: 'yellow',
                    0.9: 'orange',
                    1.0: 'red'
                }
            }).addTo(map);
        }

        // Initialize user heatmap if data exists
        if (userHeatmapData.length > 0) {
            initHeatmap('userHeatmap', userHeatmapData);
        }

        // Initialize all users heatmap if data exists
        if (allHeatmapData.length > 0) {
            initAllUsersHeatmap('allHeatmap', allHeatmapData);
        }
        
        // Create daily chart
        const days = Object.keys(dailyData);
        const values = Object.values(dailyData);
        const dayLabels = days.map(d => new Date(d).getDate());
        
        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: dayLabels,
                datasets: [{
                    label: '{{ __('messages.monthly.sneezes') }}',
                    data: values,
                    backgroundColor: 'rgba(29, 70, 56, 0.7)',
                    borderColor: primaryColor,
                    borderWidth: 2,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '{{ __('messages.monthly.day_of_month') }}'
                        }
                    }
                }
            }
        });
        
        // Create user daily chart if element exists
        const userDailyChartElement = document.getElementById('userDailyChart');
        if (userDailyChartElement) {
            const userValues = Object.values(userDailyData);
            new Chart(userDailyChartElement, {
                type: 'bar',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: '{{ __('messages.monthly.sneezes') }}',
                        data: userValues,
                        backgroundColor: 'rgba(29, 70, 56, 0.7)',
                        borderColor: primaryColor,
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            title: {
                                display: true,
                                text: '{{ __('messages.monthly.day_of_month') }}'
                            }
                        }
                    }
                }
            });
        }
        
        // Initialize DataTable for user sneezes
        $(document).ready(function() {
            $('#userSneezesTable').DataTable({
                pageLength: 20,
                order: [[0, 'desc'], [1, 'desc']],
                language: {
                    search: "{{ __('messages.datatables.search_sneezes') }}:",
                    lengthMenu: "{{ __('messages.datatables.show_sneezes') }}",
                    info: "{{ __('messages.datatables.showing_sneezes') }}",
                    infoEmpty: "{{ __('messages.datatables.no_sneezes') }}",
                    infoFiltered: "{{ __('messages.datatables.filtered_sneezes') }}"
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
