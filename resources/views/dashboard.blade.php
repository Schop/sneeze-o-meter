<x-app-layout>
    <x-slot name="title">{{ __('messages.dashboard.title') }}</x-slot>
    
    <div class="py-4">

        <!-- Today's Stats, Monthly Overview, Summary Statistics, and Profile Picture -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="text-muted mb-3">{{ __('messages.dashboard.today_stats') }}</h5>
                        <table class="compact-stats-table w-100">
                            <tbody>
                                <tr>
                                    <td>{{ __('messages.dashboard.today_sneezes') }}</td>
                                    <td>{{ $todaySneezeCount }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.today_events') }}</td>
                                    <td>{{ $todaySneezeEvents }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.last_sneeze') }}</td>
                                    <td>{{ $lastSneeze ? $lastSneeze->sneeze_time : __('messages.dashboard.no_sneezes_yet') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="text-muted mb-3">{{ __('messages.dashboard.summary_statistics') }}</h5>
                        <table class="compact-stats-table w-100">
                            <tbody>
                                <tr>
                                    <td>{{ __('messages.dashboard.total_sneezes') }}</td>
                                    <td>{{ $totalSneezes }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.total_events') }}</td>
                                    <td>{{ $totalEvents }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.avg_per_day') }}</td>
                                    <td>{{ $avgPerDay }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.peak_day') }} ({{ $peakDayFormatted }})</td>
                                    <td>{{ $peakDay }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <h5 class="text-muted mb-3"><i class="bi bi-calendar-month"></i> {{ __('messages.dashboard.monthly_overview') }}</h5>
                        <table class="compact-stats-table w-100">
                            <tbody>
                                <tr>
                                    <td>{{ __('messages.dashboard.months_logged') }}</td>
                                    <td>{{ $totalMonths }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.avg_per_month') }}</td>
                                    <td>{{ $avgPerMonth }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.peak_month') }}</td>
                                    <td>{{ $peakMonthFormatted }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('messages.dashboard.peak_month_count') }}</td>
                                    <td>{{ $peakMonthCount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="text-muted mb-3 text-center"><i class="bi bi-person-circle"></i> {{ __('messages.dashboard.profile') }}</h5>
                        <div class="text-center flex-grow-1 d-flex flex-column justify-content-center">
                            <div class="mb-3">
                                <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <h6 class="mb-2">{{ auth()->user()->name }}</h6>
                            <p class="text-muted small mb-3">{{ __('messages.dashboard.member_since') }} {{ auth()->user()->created_at->format('M Y') }}</p>
                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> {{ __('messages.dashboard.edit') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1: Daily + Monthly -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <div class="card chart-card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold mb-3"><i class="bi bi-graph-up"></i> {{ __('messages.dashboard.daily_sneeze_count') }}</h5>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="switchDailyChart(7)">{{ __('messages.dashboard.last_7_days') }}</button>
                                <button type="button" class="btn btn-outline-primary active" onclick="switchDailyChart(30)">{{ __('messages.dashboard.last_30_days') }}</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchDailyChart(90)">{{ __('messages.dashboard.last_90_days') }}</button>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="timelineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card chart-card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold mb-3"><i class="bi bi-calendar3"></i> {{ __('messages.dashboard.monthly_sneeze_count') }}</h5>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="switchMonthlyChart('year')">{{ __('messages.dashboard.this_year') }}</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchMonthlyChart('all')">{{ __('messages.dashboard.all_time') }}</button>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2: Day of Week + Hourly -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <div class="card chart-card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold mb-3"><i class="bi bi-calendar-week"></i> {{ __('messages.dashboard.sneezes_by_day_of_week') }}</h5>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="switchDayOfWeekChart('year')">{{ __('messages.dashboard.this_year') }}</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchDayOfWeekChart('all')">{{ __('messages.dashboard.all_time') }}</button>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="dayOfWeekChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-xl-6">
                <div class="card chart-card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold mb-3"><i class="bi bi-clock"></i> {{ __('messages.dashboard.sneezes_by_hour') }}</h5>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="switchHourlyChart('year')">{{ __('messages.dashboard.this_year') }}</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchHourlyChart('all')">{{ __('messages.dashboard.all_time') }}</button>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="hourlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3: Location Bar Graph and Heatmap -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-6">
                <div class="card chart-card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold mb-3"><i class="bi bi-geo-alt"></i> {{ __('messages.dashboard.sneezes_by_location') }}</h5>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="switchLocationChart('year')">{{ __('messages.dashboard.this_year') }}</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchLocationChart('all')">{{ __('messages.dashboard.all_time') }}</button>
                            </div>
                        </div>
                        <p class="fs-6 text-muted text-center mb-3">{{ __('messages.dashboard.top_10_locations') }}</p>
                        <div style="height: 400px;">
                            <canvas id="locationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card chart-card shadow h-100">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold mb-3"><i class="bi bi-map"></i> {{ __('messages.dashboard.sneeze_heatmap') }}</h5>
                        <div class="d-flex justify-content-center mb-3">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active" onclick="switchHeatmap('year')">{{ __('messages.dashboard.this_year') }}</button>
                                <button type="button" class="btn btn-outline-primary" onclick="switchHeatmap('all')">{{ __('messages.dashboard.all_time') }}</button>
                            </div>
                        </div>
                        <p class="fs-6 text-muted text-center mb-3">{{ __('messages.dashboard.geographic_distribution') }}</p>
                        <div id="heatmap" style="height: 400px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sneeze History -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="card-title mb-0"><i class="bi bi-list-ul"></i> {{ __('messages.dashboard.your_sneeze_history') }}</h3>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordSneezeModal">
                        <i class="bi bi-plus-circle"></i> {{ __('messages.nav.record_sneeze') }}
                    </button>
                </div>

                @if($sneezes->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #dee2e6;"></i>
                        <p class="text-muted mt-3">{{ __('messages.dashboard.no_sneezes_yet') }}</p>
                    </div>
                @else
                        <div class="table-responsive">
                            <table id="sneezeHistoryTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            {{ __('messages.dashboard.date') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('messages.dashboard.time') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('messages.dashboard.count') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('messages.dashboard.location') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('messages.dashboard.coordinates') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('messages.dashboard.notes') }}
                                        </th>
                                        <th scope="col">
                                            {{ __('messages.dashboard.actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sneezes as $sneeze)
                                        <tr>
                                            <td data-order="{{ $sneeze->sneeze_date->format('Y-m-d') }}">
                                                {{ $sneeze->sneeze_date->format('M j, Y') }}
                                            </td>
                                            <td data-order="{{ substr($sneeze->sneeze_time, 0, 5) }}">
                                                {{ substr($sneeze->sneeze_time, 0, 5) }}
                                            </td>
                                            <td>
                                                {{ $sneeze->count }}
                                            </td>
                                            <td>
                                                {{ $sneeze->location ?: '-' }}
                                            </td>
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
                                            <td>
                                                {{ $sneeze->notes ?: '-' }}
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-link btn-sm text-primary p-0 me-2"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editSneezeModal"
                                                        data-id="{{ $sneeze->id }}"
                                                        data-date="{{ $sneeze->sneeze_date->format('Y-m-d') }}"
                                                        data-time="{{ substr($sneeze->sneeze_time, 0, 5) }}"
                                                        data-location="{{ $sneeze->location }}"
                                                        data-latitude="{{ $sneeze->latitude }}"
                                                        data-longitude="{{ $sneeze->longitude }}"
                                                        data-count="{{ $sneeze->count }}"
                                                        data-notes="{{ $sneeze->notes }}">
                                                    {{ __('messages.dashboard.edit') }}
                                                </button>
                                                <form method="POST" action="{{ route('sneezes.destroy', $sneeze) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-link btn-sm text-danger p-0"
                                                            onclick="return confirm('{{ __('messages.dashboard.confirm_delete') }}')">
                                                        {{ __('messages.dashboard.delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="border-top pt-4 text-center mt-4">
                <p class="mt-3 mb-0 text-muted"><small>{{ __('messages.footer.version') }} | {{ now()->format('F Y') }} | {{ __('messages.footer.recording_with_dedication') }}</small></p>
                <p class="text-muted mb-1 small">{{ __('messages.footer.last_updated') }}: <span id="lastUpdate">{{ now()->format('d/m/Y, H:i:s') }}</span></p>
                <p class="text-muted small">{{ __('messages.footer.last_sneeze_logged') }}: <span id="lastSneeze">{{ $lastSneezeFormatted }}</span></p>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet.heat plugin -->
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const primaryColor = '#1d4638';
        
        // Auto-detect coordinates when modal opens
        document.getElementById('recordSneezeModal').addEventListener('shown.bs.modal', function () {
            @if(auth()->user()->use_precise_location)
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            
            // Only auto-fill if fields are empty
            if (!latitudeInput.value && !longitudeInput.value) {
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
        
        // Daily Timeline Chart with multiple time periods
        const dailyData7 = @json($dailyCounts7->toArray());
        const dailyData30 = @json($dailyCounts30->toArray());
        const dailyData90 = @json($dailyCounts90->toArray());
        
        let timelineChart;
        
        function formatDailyLabels(data) {
            return Object.keys(data).map(date => {
                const d = new Date(date);
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return `${days[d.getDay()]} ${d.getDate()} ${months[d.getMonth()]}`;
            });
        }
        
        function createDailyChart(data) {
            const labels = formatDailyLabels(data);
            const values = Object.values(data);
            const dates = Object.keys(data);
            
            if (timelineChart) {
                timelineChart.destroy();
            }
            
            timelineChart = new Chart(document.getElementById('timelineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Sneezes',
                        data: values,
                        borderColor: primaryColor,
                        backgroundColor: 'rgba(29, 70, 56, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: primaryColor,
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
                            ticks: { stepSize: 1 }
                        }
                    },
                    onClick: (event, elements) => {
                        if (elements.length > 0) {
                            const index = elements[0].index;
                            const date = dates[index];
                            window.location.href = `/daily-details/${date}`;
                        }
                    },
                    onHover: (event, elements) => {
                        event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                    }
                }
            });
        }
        
        function switchDailyChart(days) {
            const clickedButton = window.event.target;
            // Update button states
            clickedButton.closest('.btn-group').querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            clickedButton.classList.add('active');
            
            // Switch data
            const dataMap = {
                7: dailyData7,
                30: dailyData30,
                90: dailyData90
            };
            createDailyChart(dataMap[days]);
        }
        
        // Initialize with 30 days
        createDailyChart(dailyData30);
        
        // Monthly Chart with year/all-time switching
        const monthlyDataYear = @json($monthlyDataThisYear->toArray());
        const monthlyDataAll = @json($monthlyDataAllTime->toArray());
        
        let monthlyChart;
        
        function createMonthlyChart(data) {
            const labels = Object.keys(data);
            const values = Object.values(data);
            const colors = labels.map((_, i) => {
                const lightness = 75 - (i / labels.length) * 40;
                return `hsl(151, 51%, ${lightness}%)`;
            });
            
            if (monthlyChart) {
                monthlyChart.destroy();
            }
            
            monthlyChart = new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sneezes',
                        data: values,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('65%)', '55%)')),
                        borderWidth: 2,
                        borderRadius: 8
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
                            ticks: { stepSize: 5 }
                        }
                    }
                }
            });
        }
        
        function switchMonthlyChart(period) {
            const clickedButton = window.event.target;
            // Update button states
            clickedButton.closest('.btn-group').querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            clickedButton.classList.add('active');
            
            // Switch data
            const dataMap = {
                'year': monthlyDataYear,
                'all': monthlyDataAll
            };
            createMonthlyChart(dataMap[period]);
        }
        
        // Initialize with this year
        createMonthlyChart(monthlyDataYear);
        
        // Day of Week Chart with year/all-time switching
        const dayOfWeekDataYear = @json($dayOfWeekCountsThisYear->toArray());
        const dayOfWeekDataAll = @json($dayOfWeekCountsAllTime->toArray());
        const dayLabels = [
            "{{ __('messages.days.monday') }}", 
            "{{ __('messages.days.tuesday') }}", 
            "{{ __('messages.days.wednesday') }}", 
            "{{ __('messages.days.thursday') }}", 
            "{{ __('messages.days.friday') }}", 
            "{{ __('messages.days.saturday') }}", 
            "{{ __('messages.days.sunday') }}"
        ];
        const dayColors = [
            'rgba(31, 69, 56, 0.9)', 'rgba(45, 106, 80, 0.9)', 'rgba(59, 143, 104, 0.9)',
            'rgba(73, 180, 128, 0.9)', 'rgba(87, 217, 152, 0.9)', 'rgba(126, 229, 179, 0.9)',
            'rgba(165, 241, 206, 0.9)'
        ];
        
        let dayOfWeekChart;
        
        function createDayOfWeekChart(data) {
            const dayValues = [
                data[1], data[2], data[3], data[4],
                data[5], data[6], data[0]
            ];
            
            if (dayOfWeekChart) {
                dayOfWeekChart.destroy();
            }
            
            dayOfWeekChart = new Chart(document.getElementById('dayOfWeekChart'), {
                type: 'bar',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: 'Sneezes',
                        data: dayValues,
                        backgroundColor: dayColors,
                        borderColor: dayColors.map(c => c.replace('0.9', '1')),
                        borderWidth: 2,
                        borderRadius: 8
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
        
        function switchDayOfWeekChart(period) {
            const clickedButton = window.event.target;
            // Update button states
            clickedButton.closest('.btn-group').querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            clickedButton.classList.add('active');
            
            // Switch data
            const dataMap = {
                'year': dayOfWeekDataYear,
                'all': dayOfWeekDataAll
            };
            createDayOfWeekChart(dataMap[period]);
        }
        
        // Initialize with this year
        createDayOfWeekChart(dayOfWeekDataYear);
        
        // Hourly Chart with year/all-time switching
        const hourlyDataYear = @json($hourlyCountsThisYear->toArray());
        const hourlyDataAll = @json($hourlyCountsAllTime->toArray());
        
        let hourlyChart;
        
        function createHourlyChart(data) {
            const hourLabels = Object.keys(data).map(h => h + ':00');
            const hourValues = Object.values(data);
            
            if (hourlyChart) {
                hourlyChart.destroy();
            }
            
            hourlyChart = new Chart(document.getElementById('hourlyChart'), {
                type: 'bar',
                data: {
                    labels: hourLabels,
                    datasets: [{
                        label: 'Sneezes',
                        data: hourValues,
                        backgroundColor: '#2d6a50',
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
        
        function switchHourlyChart(period) {
            const clickedButton = window.event.target;
            // Update button states
            clickedButton.closest('.btn-group').querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            clickedButton.classList.add('active');
            
            // Switch data
            const dataMap = {
                'year': hourlyDataYear,
                'all': hourlyDataAll
            };
            createHourlyChart(dataMap[period]);
        }
        
        // Initialize with this year
        createHourlyChart(hourlyDataYear);
        
        // Location Chart with year/all-time switching
        const locationDataYear = @json($locationStatsThisYear->toArray());
        const locationDataAll = @json($locationStatsAllTime->toArray());
        
        let locationChart;
        
        function createLocationChart(data) {
            const labels = Object.keys(data);
            const values = Object.values(data);
            const colors = labels.map((_, i) => {
                const lightness = 35 + (i * (40 / labels.length));
                return `hsl(151, 51%, ${lightness}%)`;
            });
            
            if (locationChart) {
                locationChart.destroy();
            }
            
            locationChart = new Chart(document.getElementById('locationChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sneezes',
                        data: values,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('60%', '50%')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }
        
        function switchLocationChart(period) {
            const clickedButton = window.event.target;
            // Update button states
            clickedButton.closest('.btn-group').querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            clickedButton.classList.add('active');
            
            // Switch data
            const dataMap = {
                'year': locationDataYear,
                'all': locationDataAll
            };
            createLocationChart(dataMap[period]);
        }
        
        // Initialize with this year
        createLocationChart(locationDataYear);
        
        // Heatmap with year/all-time switching
        const heatmapDataYear = @json($heatmapDataThisYear);
        const heatmapDataAll = @json($heatmapDataAllTime);
        
        let map;
        let heatLayer;
        let markers = [];
        
        function createHeatmap(data) {
            // If map exists, clear it
            if (map) {
                map.eachLayer(layer => {
                    if (layer instanceof L.TileLayer === false) {
                        map.removeLayer(layer);
                    }
                });
                markers = [];
            } else {
                // Initialize map
                map = L.map('heatmap').setView([52.0, 5.0], 7); // Default center on Netherlands
                
                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 18
                }).addTo(map);
            }
            
            if (data.length > 0) {
                // Prepare data for heatmap - format: [lat, lng, intensity]
                const heatPoints = data.map(point => [
                    point.lat,
                    point.lng,
                    point.count // Use sneeze count as intensity
                ]);
                
                // Add heatmap layer
                L.heatLayer(heatPoints, {
                    radius: 25,
                    blur: 15,
                    maxZoom: 17,
                    max: Math.max(...heatPoints.map(p => p[2])), // Use max count for proper scaling
                    gradient: {
                        0.0: '#1d4638',
                        0.2: '#2d6a50',
                        0.4: '#3b8f68',
                        0.6: '#49b480',
                        0.8: '#57d998',
                        1.0: '#7ee5b3'
                    }
                }).addTo(map);
                
                // Calculate bounds to fit all points
                const bounds = L.latLngBounds(data.map(p => [p.lat, p.lng]));
                map.fitBounds(bounds, { padding: [50, 50] });
                
                // Get max count for scaling
                const maxCount = Math.max(...data.map(p => p.count));
                const minCount = Math.min(...data.map(p => p.count));
                
                // Add markers with popups - size and color based on count
                data.forEach(point => {
                    // Scale radius between 4 and 15 based on count
                    const normalizedCount = maxCount > minCount 
                        ? (point.count - minCount) / (maxCount - minCount)
                        : 0.5;
                    const radius = 4 + (normalizedCount * 11); // 4 to 15
                    
                    // Color gradient from green (low) to yellow to red (high)
                    const getColor = (value) => {
                        if (value < 0.25) return '#00ff00'; // Green
                        if (value < 0.5) return '#7fff00';  // Yellow-green
                        if (value < 0.75) return '#ffff00'; // Yellow
                        if (value < 0.9) return '#ff7f00';  // Orange
                        return '#ff0000'; // Red
                    };
                    
                    const marker = L.circleMarker([point.lat, point.lng], {
                        radius: radius,
                        fillColor: getColor(normalizedCount),
                        color: '#fff',
                        weight: 2,
                        opacity: 0.9,
                        fillOpacity: 0.7
                    }).addTo(map);
                    
                    marker.bindPopup(`
                        <strong>${point.location || 'Unknown Location'}</strong><br>
                        Total Sneezes: ${point.count}<br>
                        Events: ${point.events}<br>
                        Coordinates: ${point.lat.toFixed(4)}, ${point.lng.toFixed(4)}
                    `);
                    
                    markers.push(marker);
                });
            } else {
                // Show message when no data
                const messageDiv = document.createElement('div');
                messageDiv.className = 'text-center text-muted';
                messageDiv.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000;';
                messageDiv.innerHTML = '<i class="bi bi-geo-alt" style="font-size: 3rem;"></i><p class="mt-2">{{ __('messages.dashboard.no_location_data') }}<br><small>{{ __('messages.dashboard.add_coordinates') }}</small></p>';
                document.getElementById('heatmap').appendChild(messageDiv);
            }
        }
        
        function switchHeatmap(period) {
            const clickedButton = window.event.target;
            // Update button states
            clickedButton.closest('.btn-group').querySelectorAll('button').forEach(btn => {
                btn.classList.remove('active');
            });
            clickedButton.classList.add('active');
            
            // Switch data
            const dataMap = {
                'year': heatmapDataYear,
                'all': heatmapDataAll
            };
            createHeatmap(dataMap[period]);
        }
        
        // Initialize with this year
        createHeatmap(heatmapDataYear);
        
        // Initialize DataTable for sneeze history
        $(document).ready(function() {
            $('#sneezeHistoryTable').DataTable({
                order: [[0, 'desc']], // Sort by date descending
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