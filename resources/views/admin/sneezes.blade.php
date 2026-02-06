<x-app-layout>
    <x-slot name="title">{{ __('messages.admin.all_sneezes') }}</x-slot>
    
    <div class="py-4">
        <!-- Header -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold"><i class="bi bi-database"></i> {{ __('messages.admin.all_sneezes') }}</h2>
                <p class="text-muted">{{ __('messages.admin.all_sneezes_description') }}</p>
            </div>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> {{ __('messages.admin.back_to_admin') }}
            </a>
        </div>

        <!-- Sneezes Table -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sneezesTable" class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('messages.admin.id') }}</th>
                                <th>{{ __('messages.admin.user') }}</th>
                                <th>{{ __('messages.dashboard.date') }}</th>
                                <th>{{ __('messages.dashboard.time') }}</th>
                                <th>{{ __('messages.dashboard.count') }}</th>
                                <th>{{ __('messages.dashboard.location') }}</th>
                                <th>{{ __('messages.dashboard.coordinates') }}</th>
                                <th>{{ __('messages.dashboard.notes') }}</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>{{ __('messages.dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sneezes as $sneeze)
                                <tr>
                                    <td>{{ $sneeze->id }}</td>
                                    <td>
                                        @if($sneeze->user)
                                            <strong>{{ $sneeze->user->name }}</strong>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td data-order="{{ $sneeze->sneeze_date }}">
                                        {{ \Carbon\Carbon::parse($sneeze->sneeze_date)->format('M d, Y') }}
                                    </td>
                                    <td>{{ substr($sneeze->sneeze_time, 0, 5) }}</td>
                                    <td>{{ $sneeze->count }}</td>
                                    <td>{{ $sneeze->location ?? '-' }}</td>
                                    <td>
                                        @if($sneeze->latitude && $sneeze->longitude)
                                            <a href="https://www.google.com/maps?q={{ $sneeze->latitude }},{{ $sneeze->longitude }}" 
                                               target="_blank" 
                                               class="text-decoration-none">
                                                {{ number_format($sneeze->latitude, 4) }}, {{ number_format($sneeze->longitude, 4) }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($sneeze->notes)
                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $sneeze->notes }}">
                                                {{ $sneeze->notes }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td data-order="{{ $sneeze->created_at }}">
                                        <small>{{ $sneeze->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td data-order="{{ $sneeze->updated_at }}">
                                        <small>{{ $sneeze->updated_at->format('M d, Y H:i') }}</small>
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
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">{{ __('messages.admin.no_sneezes') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $sneezes->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#sneezesTable').DataTable({
                paging: false,
                info: false,
                order: [[0, 'desc']],
                language: {
                    search: "{{ __('messages.admin.search') }}:"
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
