<x-app-layout>
    <x-slot name="title">{{ __('messages.home.title') }}</x-slot>
    
    <div class="py-4">
        @guest
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info text-center mx-auto"">
                    <strong>{{ __('messages.home.cta_guest_title') }}</strong><br>
                    {{ __('messages.home.cta_guest_subtitle') }}<br>
                    <span class="d-block mt-2">
                        <strong>{{ __('messages.home.cta_guest_privacy_title', [], app()->getLocale()) }}</strong> {!! __('messages.home.cta_guest_privacy_text', [], app()->getLocale()) !!}<br>
                        <p>{!! __('messages.home.cta_guest_privacy_use_statistics', [], app()->getLocale()) !!}  </p>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm mt-2">{{ __('messages.home.sign_up') }}</a>
                    </span>
                </div>
            </div>
        </div>
        @endguest
        <!-- First Row: Leaderboards -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <h5 class="mb-0">{{ __('messages.home.today_leader') }}</h5>
                        </div>
                        @if($todayTopSneezers->count() > 0)
                            @php $leader = $todayTopSneezers->first(); @endphp
                            <div class="text-center flex-grow-1 d-flex flex-column justify-content-center">
                                <div class="mb-3">
                                    <img src="{{ $leader->profile_picture_url }}" alt="{{ $leader->name }}" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                                <h5 class="mb-2">{{ $leader->name }}</h5>
                                <div class="text-primary fw-bold fs-5">{{ $leader->sneeze_count }} {{ $leader->sneeze_count == 1 ? __('messages.home.sneeze') : __('messages.home.sneezes') }}</div>
                                @if(auth()->check() && $leader->id === auth()->id())
                                    <small class="text-muted">{{ __('messages.general.you') }}</small>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 text-center">
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    <a href="{{ route('leaderboard') }}?type=daily" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-trophy"></i> {{ __('messages.home.view_full_leaderboard') }}
                                    </a>
                                    @auth
                                    @if($leader && $leader->id === auth()->id())
                                    <button class="btn btn-sm btn-success" onclick="openShareModal(1, 'daily', '{{ \Carbon\Carbon::today()->format('Y-m-d') }}')">
                                        <i class="bi bi-share"></i> {{ __('messages.leaderboard.share') }}
                                    </button>
                                    @endif
                                    @endauth
                                </div>
                            </div>
                        @else
                            <div class="text-center flex-grow-1 d-flex flex-column justify-content-center">
                                <div class="mb-3">
                                    <img src="{{ asset('images/avatar-placeholder.svg') }}" alt="No leader yet" class="rounded-circle opacity-50" style="width: 120px; height: 120px;">
                                </div>
                                <p class="text-muted">{{ __('messages.home.no_data') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100" id="day-leaderboard-card">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <button onclick="navigateDay(-1)" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <h5 class="mb-0 text-center flex-grow-1" id="day-title">
                                @if(\Carbon\Carbon::parse($selectedDate)->isYesterday())
                                    {{ __('messages.home.yesterday') }}
                                @else
                                    @if(app()->getLocale() === 'nl')
                                        {{ \App\Helpers\DateHelper::formatLocalized(\Carbon\Carbon::parse($selectedDate), 'j M Y') }}
                                    @else
                                        {{ \App\Helpers\DateHelper::formatLocalized(\Carbon\Carbon::parse($selectedDate), 'M j, Y') }}
                                    @endif
                                @endif
                            </h5>
                            <button onclick="navigateDay(1)" class="btn btn-sm btn-outline-secondary" id="day-next-btn" @if(\Carbon\Carbon::parse($selectedDate)->isToday() || \Carbon\Carbon::parse($selectedDate)->isFuture()) disabled @endif>
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div id="day-content" class="d-flex flex-column flex-grow-1">
                        @if($yesterdayTopSneezers->count() > 0)
                            <div class="list-group list-group-flush flex-grow-1">
                                @foreach($yesterdayTopSneezers as $index => $sneezer)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center @if(auth()->check() && $sneezer->id === auth()->id()) bg-info bg-opacity-10 @endif">
                                        <span>
                                            <strong>{{ $index + 1 }}</strong> {{ Str::limit($sneezer->name, 15) }}@if(auth()->check() && $sneezer->id === auth()->id()) ({{ __('messages.general.you') }})@endif
                                        </span>
                                        <span>{{ $sneezer->sneeze_count }}</span>
                                    </div>
                                @endforeach
                                @if($currentUserYesterday)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center bg-info bg-opacity-10 border-top">
                                        <span>
                                            <strong>{{ $currentUserYesterday->rank }}</strong> {{ Str::limit($currentUserYesterday->name, 15) }} ({{ __('messages.general.you') }})
                                        </span>
                                        <span>{{ $currentUserYesterday->sneeze_count }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 text-center">
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    <a href="{{ route('leaderboard') }}?type=daily" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-trophy"></i> {{ __('messages.home.view_full_leaderboard') }}
                                    </a>
                                    @auth
                                    @if($currentUserYesterday && $currentUserYesterday->rank <= 10)
                                    <button class="btn btn-sm btn-success" onclick="openShareModal({{ $currentUserYesterday->rank }}, 'daily', '{{ $selectedDate }}')">
                                        <i class="bi bi-share"></i> {{ __('messages.leaderboard.share') }}
                                    </button>
                                    @endif
                                    @endauth
                                </div>
                            </div>
                        @else
                            <p class="text-muted">{{ __('messages.home.no_data') }}</p>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100" id="month-leaderboard-card">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <button onclick="navigateMonth(-1)" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <h5 class="mb-0 text-center flex-grow-1" id="month-title">
                                {{ \App\Helpers\DateHelper::formatLocalized(\Carbon\Carbon::parse($selectedMonth), 'F Y') }}
                            </h5>
                            <button onclick="navigateMonth(1)" class="btn btn-sm btn-outline-secondary" id="month-next-btn" @if(\Carbon\Carbon::parse($selectedMonth)->isCurrentMonth() || \Carbon\Carbon::parse($selectedMonth)->isFuture()) disabled @endif>
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                        <div id="month-content" class="d-flex flex-column flex-grow-1">
                        @if($monthTopSneezers->count() > 0)
                            <div class="list-group list-group-flush flex-grow-1">
                                @foreach($monthTopSneezers as $index => $sneezer)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center @if(auth()->check() && $sneezer->id === auth()->id()) bg-info bg-opacity-10 @endif">
                                        <span>
                                            <strong>{{ $index + 1 }}</strong> {{ Str::limit($sneezer->name, 15) }}@if(auth()->check() && $sneezer->id === auth()->id()) ({{ __('messages.general.you') }})@endif
                                        </span>
                                        <span>{{ $sneezer->sneeze_count }}</span>
                                    </div>
                                @endforeach
                                @if($currentUserMonth)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center bg-info bg-opacity-10 border-top">
                                        <span>
                                            <strong>{{ $currentUserMonth->rank }}</strong> {{ Str::limit($currentUserMonth->name, 15) }} ({{ __('messages.general.you') }})
                                        </span>
                                        <span>{{ $currentUserMonth->sneeze_count }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 text-center">
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    <a href="{{ route('leaderboard') }}?type=monthly" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-trophy"></i> {{ __('messages.home.view_full_leaderboard') }}
                                    </a>
                                    @auth
                                    @if($currentUserMonth && $currentUserMonth->rank <= 10)
                                    <button class="btn btn-sm btn-success" onclick="openShareModal({{ $currentUserMonth->rank }}, 'monthly', '{{ $selectedMonth }}')">
                                        <i class="bi bi-share"></i> {{ __('messages.leaderboard.share') }}
                                    </button>
                                    @endif
                                    @endauth
                                </div>
                            </div>
                        @else
                            <p class="text-muted">{{ __('messages.home.no_data') }}</p>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <i class="bi bi-trophy-fill text-warning fs-3 me-2"></i>
                            <h5 class="mb-0">{{ __('messages.home.overall_leader') }}</h5>
                        </div>
                        @if($topSneezers->count() > 0)
                            <div class="list-group list-group-flush flex-grow-1">
                                @foreach($topSneezers as $index => $sneezer)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center @if(auth()->check() && $sneezer->id === auth()->id()) bg-info bg-opacity-10 @endif">
                                        <span>
                                            <strong>{{ $index + 1 }}</strong> {{ Str::limit($sneezer->name, 15) }}@if(auth()->check() && $sneezer->id === auth()->id()) ({{ __('messages.general.you') }})@endif
                                        </span>
                                        <span>{{ $sneezer->sneeze_count }}</span>
                                    </div>
                                @endforeach
                                @if($currentUserOverall)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center bg-info bg-opacity-10 border-top">
                                        <span>
                                            <strong>{{ $currentUserOverall->rank }}</strong> {{ Str::limit($currentUserOverall->name, 15) }} ({{ __('messages.general.you') }})
                                        </span>
                                        <span>{{ $currentUserOverall->sneeze_count }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 text-center">
                                <a href="{{ route('leaderboard') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-trophy"></i> {{ __('messages.home.view_full_leaderboard') }}
                                </a>
                            </div>
                        @else
                            <p class="text-muted">{{ __('messages.home.no_data') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily and Monthly Trend Charts -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold mb-3">
                            <i class="bi bi-graph-up-arrow"></i> {{ __('messages.home.daily_trend') }}
                        </h5>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="switchDailyTrend(7, this)">{{ __('messages.dashboard.last_7_days') }}</button>
                                <button type="button" class="btn btn-outline-primary active" onclick="switchDailyTrend(30, this)">{{ __('messages.dashboard.last_30_days') }}</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchDailyTrend(90, this)">{{ __('messages.dashboard.last_90_days') }}</button>
                            </div>
                        </div>
                        <div class="flex-grow-1" style="min-height: 300px;">
                            <canvas id="dailyTrendChart"></canvas>
                        </div>
                        <p class="text-muted small text-center mt-2 mb-0">
                            <i class="bi bi-cursor"></i> {{ __('messages.home.click_day_for_details') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-6">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold mb-3">
                            <i class="bi bi-calendar3"></i> {{ __('messages.home.monthly_trend') }}
                        </h5>
                        @if(isset($monthlyData) && $monthlyData->count() > 0)
                        <div class="flex-grow-1" style="min-height: 300px;">
                            <canvas id="monthlyTrendChart"></canvas>
                        </div>
                        <p class="text-muted small text-center mt-2 mb-0">
                            <i class="bi bi-cursor"></i> {{ __('messages.home.click_month_for_details') }}
                        </p>
                        @else
                        <div class="text-center text-muted py-5">
                            <p>{{ __('messages.home.no_data') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sneeze Heatmap -->
        @if(isset($sneezeLocations) && $sneezeLocations->count() > 0)
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-8">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">
                            <i class="bi bi-geo-alt-fill"></i> {{ __('messages.home.sneeze_heatmap') }}
                            <span class="badge bg-secondary ms-2">{{ $sneezeLocations->count() }} {{ __('messages.home.locations') }}</span>
                        </h5>
                        <p class="text-muted small mb-3">{{ __('messages.home.heatmap_privacy_note') }}</p>
                        <div id="sneezeMap" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3"><i class="bi bi-clipboard-data"></i> {{ __('messages.home.general_stats') }}</h5>
                        <table class="compact-stats-table w-100 mb-4">
                            <tbody>
                                <tr>
                                    <td><i class="bi bi-people-fill text-primary me-2"></i>{{ __('messages.home.active_users') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($totalUsers) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-droplet-fill text-primary me-2"></i>{{ __('messages.home.total_sneezes') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($totalSneezes) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-list-ol text-primary me-2"></i>{{ __('messages.home.total_events') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($totalEvents) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <div class="text-center mb-3">
                            <i class="bi bi-graph-up text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">{{ __('messages.home.top_day') }}</h6>
                            @if($topDay)
                                <p class="fw-bold text-primary mb-0">{{ number_format($topDay->total) }} {{ __('messages.home.sneezes') }}</p>
                                <p class="text-muted small">{{ \App\Helpers\DateHelper::formatLocalized(\Carbon\Carbon::parse($topDay->sneeze_date)) }}</p>
                            @else
                                <p class="text-muted small">{{ __('messages.home.no_data') }}</p>
                            @endif
                        </div>
                        <hr>
                        <div class="text-center">
                            <i class="bi bi-bar-chart text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2 mb-1">{{ __('messages.home.top_month') }}</h6>
                            @if($topMonth)
                                <p class="fw-bold text-primary mb-0">{{ number_format($topMonth->total) }} {{ __('messages.home.sneezes') }}</p>
                                <p class="text-muted small">{{ \Carbon\Carbon::parse($topMonth->month . '-01')->translatedFormat('F Y') }}</p>
                            @else
                                <p class="text-muted small">{{ __('messages.home.no_data') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Daily trend chart data - defined globally
        const dailyData7 = @json($dailyCounts7);
        const dailyData30 = @json($dailyCounts30);
        const dailyData90 = @json($dailyCounts90);
        
        let dailyTrendChart = null;
        
        function createDailyTrend(days) {
            const data = days === 7 ? dailyData7 : (days === 30 ? dailyData30 : dailyData90);
            const labels = Object.keys(data);
            const values = Object.values(data);
            
            if (dailyTrendChart) {
                dailyTrendChart.destroy();
            }
            
            const ctx = document.getElementById('dailyTrendChart');
            dailyTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.map(date => {
                        const d = new Date(date);
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: [{
                        label: 'Sneezes',
                        data: values,
                        borderColor: '#1d4638',
                        backgroundColor: 'rgba(29, 70, 56, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#1d4638',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
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
                            ticks: { 
                                stepSize: 5,
                                precision: 0
                            }
                        }
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const date = labels[index];
                            window.location.href = `/daily-details/${date}`;
                        }
                    },
                    onHover: (event, elements) => {
                        event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                    }
                }
            });
        }
        
        function switchDailyTrend(days, button) {
            // Update button states
            const buttons = button.closest('.btn-group').querySelectorAll('button');
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Update chart
            createDailyTrend(days);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize with 30 days
            createDailyTrend(30);
            
            // Monthly trend chart
            @if(isset($monthlyData) && $monthlyData->count() > 0)
            const monthlyData = @json($monthlyData->values());
            const monthlyLabels = monthlyData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            });
            const monthlyValues = monthlyData.map(item => item.total);
            
            new Chart(document.getElementById('monthlyTrendChart'), {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Total Sneezes',
                        data: monthlyValues,
                        backgroundColor: 'rgba(29, 70, 56, 0.7)',
                        borderColor: '#1d4638',
                        borderWidth: 1,
                        borderRadius: 5,
                        hoverBackgroundColor: 'rgba(29, 70, 56, 0.9)'
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
                            ticks: { 
                                stepSize: 10,
                                precision: 0
                            }
                        }
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const monthStr = monthlyData[index].month;
                            window.location.href = `/monthly-details/${monthStr}`;
                        }
                    },
                    onHover: (event, elements) => {
                        event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                    }
                }
            });
            @endif
            
            @if(isset($sneezeLocations) && $sneezeLocations->count() > 0)
            // Initialize map
            const sneezeLocations = @json($sneezeLocations);
            
            console.log('Sneeze locations:', sneezeLocations);
            
            // Calculate bounds of all points
            const lats = sneezeLocations.map(loc => loc.lat);
            const lngs = sneezeLocations.map(loc => loc.lng);
            const bounds = [
                [Math.min(...lats), Math.min(...lngs)],
                [Math.max(...lats), Math.max(...lngs)]
            ];
            
            // Initialize map
            const map = L.map('sneezeMap');
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18
            }).addTo(map);
            
            // Fit map to bounds with padding
            map.fitBounds(bounds, { padding: [50, 50] });
            
            // Prepare heatmap data: [lat, lng, intensity]
            const heatData = sneezeLocations.map(loc => [loc.lat, loc.lng, loc.intensity]);
            
            console.log('Heat data:', heatData);
            
            // Add heatmap layer with adjusted settings for better visibility
            const heat = L.heatLayer(heatData, {
                radius: 40,
                blur: 25,
                maxZoom: 17,
                max: 10, // Lower max value makes colors more intense
                minOpacity: 0.5, // Minimum opacity to ensure visibility
                gradient: {
                    0.0: 'blue',
                    0.3: 'cyan',
                    0.5: 'lime',
                    0.7: 'yellow',
                    0.9: 'orange',
                    1.0: 'red'
                }
            }).addTo(map);
            
            // Invalidate size to ensure proper rendering
            setTimeout(() => map.invalidateSize(), 100);
            @endif
        });
        
        // AJAX navigation for leaderboards
        let currentDate = '{{ $selectedDate }}';
        let currentMonth = '{{ $selectedMonth }}';
        const authUserId = {{ auth()->check() ? auth()->id() : 'null' }};
        const youLabel = '{{ __("messages.general.you") }}';
        const sneezesLabel = '{{ __("messages.home.sneezes") }}';
        const noDataLabel = '{{ __("messages.home.no_data") }}';
        const yesterdayLabel = '{{ __("messages.home.yesterday") }}';
        
        function isYesterday(dateString) {
            const date = new Date(dateString);
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            return date.toDateString() === yesterday.toDateString();
        }
        
        function navigateDay(direction) {
            const date = new Date(currentDate);
            date.setDate(date.getDate() + direction);
            const newDate = date.toISOString().split('T')[0];
            
            fetch(`{{ route('leaderboard.data') }}?type=day&date=${newDate}`)
                .then(response => response.json())
                .then(data => {
                    currentDate = data.date;
                    updateDayContent(data);
                    updateURL();
                })
                .catch(error => console.error('Error:', error));
        }
        
        function navigateMonth(direction) {
            const [year, month] = currentMonth.split('-');
            const date = new Date(year, month - 1, 1);
            date.setMonth(date.getMonth() + direction);
            const newMonth = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            
            fetch(`{{ route('leaderboard.data') }}?type=month&month=${newMonth}`)
                .then(response => response.json())
                .then(data => {
                    currentMonth = data.month;
                    updateMonthContent(data);
                    updateURL();
                })
                .catch(error => console.error('Error:', error));
        }
        
        function updateDayContent(data) {
            const displayDate = isYesterday(data.date) ? yesterdayLabel : data.formatted_date;
            document.getElementById('day-title').textContent = displayDate;
            
            let html = '';
            if (data.top_sneezers.length > 0) {
                html = '<div class="list-group list-group-flush flex-grow-1">';
                data.top_sneezers.forEach((sneezer, index) => {
                    const isCurrentUser = authUserId && sneezer.id === authUserId;
                    html += `
                        <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center ${isCurrentUser ? 'bg-info bg-opacity-10' : ''}">
                            <span class="d-flex align-items-center">
                                <img src="${sneezer.profile_picture_url}" alt="${sneezer.name}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                <strong>${index + 1}</strong> ${sneezer.name}${isCurrentUser ? ' (' + youLabel + ')' : ''}
                            </span>
                            <span>${sneezer.sneeze_count} ${sneezesLabel}</span>
                        </div>
                    `;
                });
                
                if (data.current_user) {
                    html += `
                        <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center bg-info bg-opacity-10 border-top">
                            <span class="d-flex align-items-center">
                                <img src="${data.current_user.profile_picture_url}" alt="${data.current_user.name}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                <strong>${data.current_user.rank}</strong> ${data.current_user.name} (${youLabel})
                            </span>
                            <span>${data.current_user.sneeze_count} ${sneezesLabel}</span>
                        </div>
                    `;
                }
                html += '</div>';
                html += `<div class="mt-auto pt-3 text-center"><a href="{{ route("leaderboard") }}?type=daily&period=${data.date}" class="btn btn-sm btn-outline-primary"><i class="bi bi-trophy"></i> {{ __("messages.home.view_full_leaderboard") }}</a></div>`;
            } else {
                html = `<p class="text-muted">${noDataLabel}</p>`;
            }
            
            document.getElementById('day-content').innerHTML = html;
            document.getElementById('day-next-btn').disabled = data.is_today || data.is_future;
        }
        
        function updateMonthContent(data) {
            document.getElementById('month-title').textContent = data.formatted_month;
            
            let html = '';
            if (data.top_sneezers.length > 0) {
                html = '<div class="list-group list-group-flush flex-grow-1">';
                data.top_sneezers.forEach((sneezer, index) => {
                    const isCurrentUser = authUserId && sneezer.id === authUserId;
                    html += `
                        <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center ${isCurrentUser ? 'bg-info bg-opacity-10' : ''}">
                            <span class="d-flex align-items-center">
                                <img src="${sneezer.profile_picture_url}" alt="${sneezer.name}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                <strong>${index + 1}</strong> ${sneezer.name}${isCurrentUser ? ' (' + youLabel + ')' : ''}
                            </span>
                            <span>${sneezer.sneeze_count} ${sneezesLabel}</span>
                        </div>
                    `;
                });
                
                if (data.current_user) {
                    html += `
                        <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center bg-info bg-opacity-10 border-top">
                            <span class="d-flex align-items-center">
                                <img src="${data.current_user.profile_picture_url}" alt="${data.current_user.name}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                <strong>${data.current_user.rank}</strong> ${data.current_user.name} (${youLabel})
                            </span>
                            <span>${data.current_user.sneeze_count} ${sneezesLabel}</span>
                        </div>
                    `;
                }
                html += '</div>';
                html += `<div class="mt-auto pt-3 text-center"><a href="{{ route("leaderboard") }}?type=monthly&period=${data.month}" class="btn btn-sm btn-outline-primary"><i class="bi bi-trophy"></i> {{ __("messages.home.view_full_leaderboard") }}</a></div>`;
            } else {
                html = `<p class="text-muted">${noDataLabel}</p>`;
            }
            
            document.getElementById('month-content').innerHTML = html;
            document.getElementById('month-next-btn').disabled = data.is_current_month || data.is_future;
        }
        
        function updateURL() {
            const params = new URLSearchParams();
            params.set('date', currentDate);
            params.set('month', currentMonth);
            const newURL = `${window.location.pathname}?${params.toString()}`;
            history.pushState({ date: currentDate, month: currentMonth }, '', newURL);
        }
        
        // Sharing functions
        let currentShareMessage = '';
        
        function openShareModal(position, type, period) {
            let message = '';
            const baseUrl = window.location.origin;
            const locale = '{{ app()->getLocale() }}';
            
            if (type === 'daily') {
                const dateStr = new Date(period).toLocaleDateString(locale === 'nl' ? 'nl-NL' : 'en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                message = locale === 'nl'
                    ? `Ik heb de ${getOrdinal(position)} plaats behaald op de ${dateStr} ranglijst bij Sneeze-o-Meter!`
                    : `I achieved ${getOrdinal(position)} place on the ${dateStr} leaderboard at Sneeze-o-Meter!`;
            } else if (type === 'monthly') {
                const monthName = new Date(period).toLocaleDateString(locale === 'nl' ? 'nl-NL' : 'en-US', { month: 'long', year: 'numeric' });
                message = locale === 'nl'
                    ? `Ik heb de ${getOrdinal(position)} plaats behaald op de ${monthName} ranglijst bij Sneeze-o-Meter!`
                    : `I achieved ${getOrdinal(position)} place on the ${monthName} leaderboard at Sneeze-o-Meter!`;
            }
            
            currentShareMessage = message + ' ' + baseUrl;
            showShareDialog(currentShareMessage);
        }
        
        function getOrdinal(n) {
            if (n === 1) return '1e';
            if (n === 2) return '2e'; 
            if (n === 3) return '3e';
            return n + 'e';
        }
        
        function showShareDialog(message) {
            // Use Web Share API if available, otherwise show modal
            if (navigator.share) {
                navigator.share({
                    title: 'Sneeze-o-Meter Leaderboard',
                    text: message,
                    url: window.location.href
                }).catch(() => {
                    // Fallback to modal
                    document.getElementById('shareMessage').textContent = message;
                    const modal = new bootstrap.Modal(document.getElementById('shareModal'));
                    modal.show();
                });
            } else {
                // Show modal with sharing options
                document.getElementById('shareMessage').textContent = message;
                const modal = new bootstrap.Modal(document.getElementById('shareModal'));
                modal.show();
            }
        }
        
        function shareOnWhatsApp() {
            const url = `https://wa.me/?text=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank');
        }
        
        function shareOnFacebook() {
            const url = `https://m.facebook.com/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank');
        }
        
        function shareOnTwitter() {
            const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank');
        }
        
        function shareOnLinkedIn() {
            const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank');
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Simple feedback - could be enhanced with a toast notification
                alert('{{ __("messages.leaderboard.share_achievement") }} copied to clipboard!');
            });
        }
    </script>
    
    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">{{ __('messages.leaderboard.share_achievement') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="shareMessage"></p>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-success btn-sm" onclick="shareOnWhatsApp()">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="shareOnFacebook()">
                            <i class="bi bi-facebook"></i> Facebook
                        </button>
                        <button class="btn btn-info btn-sm" onclick="shareOnTwitter()">
                            <i class="bi bi-twitter"></i> Twitter
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="shareOnLinkedIn()">
                            <i class="bi bi-linkedin"></i> LinkedIn
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="copyToClipboard(currentShareMessage)">
                            <i class="bi bi-clipboard"></i> {{ __('messages.general.copy') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @endpush
</x-app-layout>
