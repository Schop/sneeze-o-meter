<x-app-layout>
    <x-slot name="title">{{ __('messages.leaderboard.title') }}</x-slot>
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title mb-0">{{ __('messages.leaderboard.top_sneezers') }}</h3>
                
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary {{ $type === 'all' ? 'active' : '' }}" onclick="changeLeaderboardType('all')">
                        {{ __('messages.leaderboard.all_time') }}
                    </button>
                    <button type="button" class="btn btn-outline-primary {{ $type === 'monthly' ? 'active' : '' }}" onclick="changeLeaderboardType('monthly')">
                        {{ __('messages.leaderboard.monthly') }}
                    </button>
                    <button type="button" class="btn btn-outline-primary {{ $type === 'daily' ? 'active' : '' }}" onclick="changeLeaderboardType('daily')">
                        {{ __('messages.leaderboard.daily') }}
                    </button>
                </div>
            </div>
            
            @if($type === 'monthly' || $type === 'daily')
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <button onclick="navigatePeriod(-1)" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <h5 class="mb-0" id="period-title">
                            @if($type === 'monthly')
                                {{ \Carbon\Carbon::parse($period)->format('F Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($period)->format('F j, Y') }}
                            @endif
                        </h5>
                        <button onclick="navigatePeriod(1)" class="btn btn-sm btn-outline-secondary" id="period-next-btn" 
                            @if(($type === 'monthly' && \Carbon\Carbon::parse($period)->isCurrentMonth()) || ($type === 'daily' && \Carbon\Carbon::parse($period)->isToday()) || \Carbon\Carbon::parse($period)->isFuture()) disabled @endif>
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
            @endif
            
            @if($leaderboard->isEmpty())
                <p class="text-muted">{{ __('messages.leaderboard.no_sneezes') }}</p>
            @else
                <div class="table-responsive">
                    <table id="leaderboardTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{ __('messages.leaderboard.rank') }}
                                </th>
                                <th scope="col">
                                    {{ __('messages.leaderboard.name') }}
                                </th>
                                <th scope="col">
                                    {{ __('messages.leaderboard.total_sneezes') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboard as $index => $user)
                                <tr class="{{ $index < 3 ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>{{ $index + 1 }}</strong>
                                    </td>
                                    <td>
                                        {{ Str::limit($user->name, 15) }}
                                    </td>
                                    <td>
                                        {{ $user->sneeze_count }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        {{ __('messages.home.go_to_dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        {{ __('messages.leaderboard.login_to_track') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentType = '{{ $type }}';
        let currentPeriod = '{{ $period }}';
        
        $(document).ready(function() {
            initDataTable();
        });
        
        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#leaderboardTable')) {
                $('#leaderboardTable').DataTable().destroy();
            }
            
            $('#leaderboardTable').DataTable({
                order: [[2, 'desc']], // Sort by total sneezes descending
                pageLength: 25,
                language: {
                    search: "{{ __('messages.datatables.search_users') }}:",
                    lengthMenu: "{{ __('messages.datatables.show_users') }}",
                    info: "{{ __('messages.datatables.showing_users') }}",
                    infoEmpty: "{{ __('messages.datatables.no_users') }}",
                    infoFiltered: "{{ __('messages.datatables.filtered_users') }}"
                },
                columnDefs: [
                    { orderable: false, targets: 0 } // Disable sorting on rank column
                ]
            });
        }
        
        function changeLeaderboardType(type) {
            if (type === 'all') {
                window.location.href = '{{ route("leaderboard") }}';
            } else {
                window.location.href = '{{ route("leaderboard") }}?type=' + type;
            }
        }
        
        function navigatePeriod(direction) {
            const url = '{{ route("leaderboard") }}?type=' + currentType + '&period=' + currentPeriod + '&navigate=' + direction;
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                currentPeriod = data.period;
                updateLeaderboardDisplay(data);
            });
        }
        
        function updateLeaderboardDisplay(data) {
            // Update period title
            document.getElementById('period-title').textContent = data.periodLabel;
            
            // Update next button state
            document.getElementById('period-next-btn').disabled = data.disableNext;
            
            // Update table
            if ($.fn.DataTable.isDataTable('#leaderboardTable')) {
                $('#leaderboardTable').DataTable().destroy();
            }
            
            const tbody = document.querySelector('#leaderboardTable tbody');
            if (data.leaderboard.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-muted">{{ __('messages.leaderboard.no_sneezes') }}</td></tr>';
            } else {
                tbody.innerHTML = data.leaderboard.map((user, index) => `
                    <tr class="${index < 3 ? 'table-warning' : ''}">
                        <td><strong>${index + 1}</strong></td>
                        <td>${user.name}</td>
                        <td>${user.sneeze_count}</td>
                    </tr>
                `).join('');
            }
            
            // Reinitialize DataTable
            if (data.leaderboard.length > 0) {
                initDataTable();
            }
        }
    </script>
    @endpush
</x-app-layout>
