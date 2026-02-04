<nav class="navbar navbar-expand-lg navbar-light rounded mb-3">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        @auth
            <button type="button" class="btn btn-primary btn-sm d-lg-none ms-auto" data-bs-toggle="modal" data-bs-target="#recordSneezeModal">
                <i class="bi bi-plus-circle"></i> {{ __('messages.nav.record_sneeze') }}
            </button>
        @endauth
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav nav-pills me-auto ps-lg-0 ps-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active text-white' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house"></i> {{ __('messages.nav.home') }}
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active text-white' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> {{ __('messages.nav.dashboard') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active text-white' : '' }}" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person"></i> {{ __('messages.nav.profile') }}
                        </a>
                    </li>
                @endauth
                @auth
                    @if(auth()->user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'active text-white' : '' }}" href="{{ route('admin.index') }}">
                                <i class="bi bi-shield-lock"></i> {{ __('messages.nav.admin') }}
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link"><i class="bi bi-box-arrow-right"></i> {{ __('messages.nav.logout') }}</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active text-white' : '' }}" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> {{ __('messages.nav.login') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active text-white' : '' }}" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> {{ __('messages.nav.register') }}</a>
                    </li>
                @endauth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('help') ? 'active text-white' : '' }}" href="{{ route('help') }}">
                            <i class="bi bi-question-circle"></i> {{ __('messages.nav.help') }}
                        </a>
                    </li>
            </ul>
            
            @auth
                <div class="d-none d-lg-block">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordSneezeModal">
                        <i class="bi bi-plus-circle"></i> {{ __('messages.nav.record_sneeze') }}
                    </button>
                </div>
            @endauth
        </div>
    </div>
</nav>