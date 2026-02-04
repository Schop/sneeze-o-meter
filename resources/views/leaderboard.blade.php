<x-app-layout>
    <x-slot name="title">{{ __('messages.leaderboard.title') }}</x-slot>
    
    <!-- Open Graph Meta Tags for Social Sharing -->
    <meta property="og:title" content="{{ __('messages.leaderboard.title') }} - {{ config('app.name') }}">
    <meta property="og:description" content="{{ __('messages.leaderboard.description', ['app' => config('app.name')]) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    
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
                                <th scope="col">
                                    {{ __('messages.leaderboard.share') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboard as $index => $user)
                                <tr class="{{ auth()->check() && auth()->id() === $user->id ? 'table-info' : '' }}">
                                    <td>
                                        <strong>{{ $index + 1 }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $user->sneeze_count }}
                                    </td>
                                    <td>
                                        @auth
                                            @if(auth()->id() === $user->id)
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openShareModal({{ $index + 1 }}, '{{ $type }}', '{{ $period }}')">
                                                    <i class="bi bi-share"></i>
                                                </button>
                                            @endif
                                        @endauth
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
                        <button class="btn btn-secondary btn-sm" onclick="copyToClipboard()">
                            <i class="bi bi-clipboard"></i> {{ __('messages.general.copy') }}
                        </button>
                    </div>
                </div>
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
                    { orderable: false, targets: 0 }, // Disable sorting on rank column
                    { orderable: false, targets: 3 }  // Disable sorting on share column
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
            const currentUserId = @auth {{ auth()->id() }} @else null @endauth;
            
            if (data.leaderboard.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-muted">{{ __('messages.leaderboard.no_sneezes') }}</td></tr>';
            } else {
                tbody.innerHTML = data.leaderboard.map((user, index) => `
                    <tr class="${currentUserId && currentUserId === user.id ? 'table-info' : ''}">
                        <td><strong>${index + 1}</strong></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${user.profile_picture_url}" alt="${user.name}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                ${user.name}
                            </div>
                        </td>
                        <td>${user.sneeze_count}</td>
                        <td>
                            ${currentUserId && currentUserId === user.id ? `<button type="button" class="btn btn-sm btn-outline-primary" onclick="openShareModal(${index + 1}, '${currentType}', '${currentPeriod}')"><i class="bi bi-share"></i></button>` : ''}
                        </td>
                    </tr>
                `).join('');
            }
            
            // Reinitialize DataTable
            if (data.leaderboard.length > 0) {
                initDataTable();
            }
        }
        
        let currentShareMessage = '';
        
        function openShareModal(position, type, period) {
            let message = '';
            const baseUrl = window.location.origin;
            const locale = '{{ app()->getLocale() }}';
            
            if (type === 'all') {
                message = locale === 'nl' 
                    ? `Ik heb de ${getOrdinal(position)} plaats behaald op de all-time ranglijst bij Sneeze-o-Meter!`
                    : `I achieved ${getOrdinal(position)} place on the all-time leaderboard at Sneeze-o-Meter!`;
            } else if (type === 'monthly') {
                const monthName = new Date(period).toLocaleDateString(locale === 'nl' ? 'nl-NL' : 'en-US', { month: 'long', year: 'numeric' });
                message = locale === 'nl'
                    ? `Ik heb de ${getOrdinal(position)} plaats behaald op de ${monthName} ranglijst bij Sneeze-o-Meter!`
                    : `I achieved ${getOrdinal(position)} place on the ${monthName} leaderboard at Sneeze-o-Meter!`;
            } else if (type === 'daily') {
                const dateStr = new Date(period).toLocaleDateString(locale === 'nl' ? 'nl-NL' : 'en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                message = locale === 'nl'
                    ? `Ik heb de ${getOrdinal(position)} plaats behaald op de ${dateStr} ranglijst bij Sneeze-o-Meter!`
                    : `I achieved ${getOrdinal(position)} place on the ${dateStr} leaderboard at Sneeze-o-Meter!`;
            }
            
            currentShareMessage = message + ' ' + baseUrl;
            document.getElementById('shareMessage').textContent = currentShareMessage;
            
            const modal = new bootstrap.Modal(document.getElementById('shareModal'));
            modal.show();
        }
        
        function getOrdinal(n) {
            const locale = '{{ app()->getLocale() }}';
            if (locale === 'nl') {
                return n + 'e'; // Dutch ordinal is just number + 'e'
            } else {
                // English ordinals
                const j = n % 10;
                const k = n % 100;
                if (j == 1 && k != 11) return n + 'st';
                if (j == 2 && k != 12) return n + 'nd';
                if (j == 3 && k != 13) return n + 'rd';
                return n + 'th';
            }
        }
        
        function shareOnWhatsApp() {
            const url = `https://wa.me/?text=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank', 'width=600,height=400');
        }
        
        function shareOnFacebook() {
            // Use direct Facebook sharing URL for custom message
            const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank');
        }
        
        function shareOnTwitter() {
            // Use direct Twitter sharing URL for custom message
            const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank');
        }
        
        function shareOnLinkedIn() {
            // Use direct LinkedIn sharing URL
            const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent(currentShareMessage)}`;
            window.open(url, '_blank');
        }
        
        function copyToClipboard() {
            navigator.clipboard.writeText(currentShareMessage).then(() => {
                // Show success feedback
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-secondary');
                }, 2000);
            });
        }
    </script>
    @endpush
</x-app-layout>
