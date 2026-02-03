<x-app-layout>
    <x-slot name="title">{{ __('messages.daily.title') }} - {{ $date->format('F j, Y') }}</x-slot>
    
    <div class="py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('daily.details', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-chevron-left"></i> {{ __('messages.daily.previous_day') }}
                            </a>
                            <div class="text-center flex-grow-1">
                                <h2 class="mb-1">
                                    {{ \App\Helpers\DateHelper::formatLocalized($date) }}
                                </h2>
                            </div>
                            <div class="d-flex gap-2">
                                @if(!$date->isToday())
                                <a href="{{ route('daily.details', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">
                                    {{ __('messages.daily.next_day') }} <i class="bi bi-chevron-right"></i>
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
                        <h5 class="text-muted mb-3"><i class="bi bi-person-circle"></i> {{ __('messages.daily.your_stats') }}</h5>
                        @auth
                        @if($userStats && $userStats['total_count'] > 0)
                        <table class="compact-stats-table w-100">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-droplet-fill text-primary me-1"></i> {{ __('messages.daily.your_sneezes') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($userStats['total_count']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-list-ol text-primary me-1"></i> {{ __('messages.daily.your_events') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($userStats['total_events']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-sunrise text-primary me-1"></i> {{ __('messages.daily.first_sneeze') }}</td>
                                    <td class="fw-bold text-primary">{{ substr($userStats['first_sneeze'], 0, 5) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-sunset text-primary me-1"></i> {{ __('messages.daily.last_sneeze') }}</td>
                                    <td class="fw-bold text-primary">{{ substr($userStats['last_sneeze'], 0, 5) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted">{{ __('messages.daily.no_data') }}</p>
                        @endif
                        @else
                        <p class="text-muted mb-3">{{ __('messages.daily.login_to_view_personal') }}</p>
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
                        <h5 class="text-muted mb-3"><i class="bi bi-clipboard-data"></i> {{ __('messages.daily.general_stats') }}</h5>
                        <table class="compact-stats-table w-100">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-people-fill text-primary me-1"></i> {{ __('messages.daily.active_users') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($generalStats['active_users']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-droplet-fill text-primary me-1"></i> {{ __('messages.daily.total_sneezes') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($generalStats['total_sneezes']) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-list-ol text-primary me-1"></i> {{ __('messages.daily.total_events') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($generalStats['total_events']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="text-muted mb-3"><i class="bi bi-trophy"></i> {{ __('messages.daily.top_sneezers') }}</h5>
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
                                <a href="{{ route('leaderboard') }}?type=daily&date={{ $date->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-trophy"></i> {{ __('messages.home.view_full_leaderboard') }}
                                </a>
                            </div>
                        @else
                            <p class="text-muted">{{ __('messages.daily.no_data') }}</p>
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
                        <h4 class="card-title mb-3">
                            <i class="bi bi-geo-alt"></i>
                            {{ __('messages.daily.your_heatmap') }}
                        </h4>
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
                        <h4 class="card-title mb-3">
                            <i class="bi bi-geo-alt"></i>
                            {{ __('messages.daily.all_users_heatmap') }}
                        </h4>
                        <div id="allHeatmap" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Hourly Distribution Charts -->
        <div class="row g-4 mb-4">
            @auth
            @if($userStats && $userStats['total_count'] > 0)
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="bi bi-graph-up"></i>
                            {{ __('messages.daily.your_hourly_distribution') }}
                        </h4>
                        <div style="height: 350px;">
                            <canvas id="userHourlyChart"></canvas>
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
                            {{ __('messages.daily.your_hourly_distribution') }}
                        </h4>
                        <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                            <div class="text-center">
                                <p class="text-muted mb-3">{{ __('messages.daily.login_to_view_personal') }}</p>
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
                            {{ __('messages.daily.your_hourly_distribution') }}
                        </h4>
                        <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                            <div class="text-center">
                                <p class="text-muted mb-3">{{ __('messages.daily.login_to_view_personal') }}</p>
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
                            {{ __('messages.daily.all_users_hourly_distribution') }}
                        </h4>
                        <div style="height: 350px;">
                            <canvas id="hourlyChart"></canvas>
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
                            {{ __('messages.daily.your_sneezes_today') }}
                        </h4>
                        <div class="table-responsive">
                            <table id="userSneezesTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
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
        const hourlyData = @json($hourlyDataComplete);
        const userHourlyData = @json($userHourlyDataComplete);
        const userHeatmapData = @json($userHeatmapData ?? []);
        const allHeatmapData = @json($allHeatmapData ?? []);

        // Initialize heatmaps
        function initHeatmap(elementId, data) {
            if (data.length === 0) return;

            // Calculate center based on data points
            const avgLat = data.reduce((sum, point) => sum + parseFloat(point.lat), 0) / data.length;
            const avgLng = data.reduce((sum, point) => sum + parseFloat(point.lng), 0) / data.length;

            const map = L.map(elementId).setView([avgLat, avgLng], 10);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);

            // Prepare heatmap data: [lat, lng, intensity]
            const heatData = data.map(point => [
                parseFloat(point.lat),
                parseFloat(point.lng),
                parseFloat(point.count)
            ]);

            L.heatLayer(heatData, {
                radius: 25,
                blur: 15,
                maxZoom: 17,
                gradient: {
                    0.0: 'blue',
                    0.5: 'lime',
                    0.7: 'yellow',
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
            initHeatmap('allHeatmap', allHeatmapData);
        }
        
        // Create hourly chart
        const hours = Array.from({length: 24}, (_, i) => i.toString().padStart(2, '0') + ':00');
        const values = Object.values(hourlyData);
        
        new Chart(document.getElementById('hourlyChart'), {
            type: 'bar',
            data: {
                labels: hours,
                datasets: [{
                    label: '{{ __('messages.daily.sneezes') }}',
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
                    }
                }
            }
        });
        
        // Create user hourly chart if element exists
        const userHourlyChartElement = document.getElementById('userHourlyChart');
        if (userHourlyChartElement) {
            const userValues = Object.values(userHourlyData);
            new Chart(userHourlyChartElement, {
                type: 'bar',
                data: {
                    labels: hours,
                    datasets: [{
                        label: '{{ __('messages.daily.sneezes') }}',
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
                        }
                    }
                }
            });
        }
        
        // Initialize DataTable for user sneezes
        $(document).ready(function() {
            $('#userSneezesTable').DataTable({
                pageLength: 20,
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
