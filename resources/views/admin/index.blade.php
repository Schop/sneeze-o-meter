<x-app-layout>
    <x-slot name="title">{{ __('messages.admin.title') }}</x-slot>
    
    <div class="py-4">
        <!-- Admin Header -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="fw-bold"><i class="bi bi-shield-lock"></i> {{ __('messages.admin.admin_dashboard') }}</h2>
                    <p class="text-muted">{{ __('messages.admin.manage_users') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.sneezes') }}" class="btn btn-primary">
                        <i class="bi bi-database"></i> {{ __('messages.admin.all_sneezes') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Database Switcher -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-server"></i> Database Switcher</h5>
                <p class="text-muted mb-3">Current database: <strong class="text-primary">{{ $currentDb }}</strong></p>
                <form action="{{ route('admin.switchDatabase') }}" method="POST" class="d-flex gap-2 align-items-center flex-wrap">
                    @csrf
                    <select name="database" class="form-select" style="max-width: 300px;">
                        <option value="database.sqlite" {{ $currentDb === 'database.sqlite' ? 'selected' : '' }}>database.sqlite (Production)</option>
                        <option value="testdatabase.sqlite" {{ $currentDb === 'testdatabase.sqlite' ? 'selected' : '' }}>testdatabase.sqlite (Test)</option>
                    </select>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to switch databases? This will affect all users immediately.')">
                        <i class="bi bi-arrow-repeat"></i> Switch Database
                    </button>
                </form>
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-exclamation-triangle"></i> Warning: Switching databases will change which data all users see. Make sure the target database exists.
                </small>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $totalUsers }}</h3>
                        <p class="text-muted mb-0">{{ __('messages.admin.total_users') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clipboard-data text-success" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $totalSneezes }}</h3>
                        <p class="text-muted mb-0">{{ __('messages.admin.total_sneezes') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $totalEvents }}</h3>
                        <p class="text-muted mb-0">{{ __('messages.admin.total_events') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calculator text-warning" style="font-size: 2rem;"></i>
                        <h3 class="mt-2 mb-0">{{ $avgSneezesPerUser }}</h3>
                        <p class="text-muted mb-0">{{ __('messages.admin.avg_per_user') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management -->
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title mb-4"><i class="bi bi-people"></i> {{ __('messages.admin.user_management') }}</h3>
                
                <div class="table-responsive">
                    <table id="userManagementTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.admin.id') }}</th>
                                <th>{{ __('messages.admin.name') }}</th>
                                <th>{{ __('messages.admin.email') }}</th>
                                <th>{{ __('messages.admin.admin') }}</th>
                                <th>{{ __('messages.admin.sneeze_events') }}</th>
                                <th>{{ __('messages.dashboard.total_sneezes') }}</th>
                                <th>{{ __('messages.admin.registered') }}</th>
                                <th>{{ __('messages.admin.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-primary ms-1">{{ __('messages.admin.you') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-danger">
                                                <i class="bi bi-shield-fill-check"></i> {{ __('messages.admin.admin') }}
                                            </span>
                                        @else
                                            {{ __('messages.admin.user') }}
                                        @endif
                                    </td>
                                    <td>{{ $user->sneezes_count }}</td>
                                    <td><strong>{{ $user->sneezes_sum_count ?? 0 }}</strong></td>
                                    <td>{{ $user->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Edit User -->
                                            <a href="{{ route('admin.editUser', $user) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i> {{ __('messages.admin.edit') }}
                                            </a>
                                            
                                            <!-- Toggle Admin -->
                                            <form method="POST" action="{{ route('admin.toggleAdmin', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $user->is_admin ? 'btn-warning' : 'btn-success' }}"
                                                        @if($user->id === auth()->id() && $user->is_admin) 
                                                            onclick="return confirm('{{ __('messages.admin.confirm_remove_admin') }}')"
                                                        @endif>
                                                    <i class="bi {{ $user->is_admin ? 'bi-shield-slash' : 'bi-shield-check' }}"></i>
                                                    {{ $user->is_admin ? __('messages.admin.remove_admin') : __('messages.admin.make_admin') }}
                                                </button>
                                            </form>

                                            <!-- Delete User -->
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.deleteUser', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('{{ __('messages.admin.confirm_delete_user', ['name' => $user->name]) }}')">
                                                        <i class="bi bi-trash"></i> {{ __('messages.admin.delete') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#userManagementTable').DataTable({
                order: [[5, 'desc']], // Sort by total sneezes descending
                pageLength: 25,
                language: {
                    search: "{{ __('messages.datatables.search_users') }}:",
                    lengthMenu: "{{ __('messages.datatables.show_users') }}",
                    info: "{{ __('messages.datatables.showing_users') }}",
                    infoEmpty: "{{ __('messages.datatables.no_users') }}",
                    infoFiltered: "{{ __('messages.datatables.filtered_users') }}"
                },
                columnDefs: [
                    { orderable: false, targets: -1 } // Disable sorting on actions column
                ]
            });
        });
    </script>
    @endpush
</x-app-layout>
