<nav class="navbar-light rounded mb-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            <ul class="nav nav-pills flex-wrap">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house"></i> {{ __('messages.nav.home') }}
                    </a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> {{ __('messages.nav.dashboard') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person"></i> {{ __('messages.nav.profile') }}
                        </a>
                    </li>
                @endauth
                @auth
                    @if(auth()->user()->is_admin)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.index') }}">
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
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> {{ __('messages.nav.login') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> {{ __('messages.nav.register') }}</a>
                    </li>
                @endauth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('help') ? 'active' : '' }}" href="{{ route('help') }}">
                            <i class="bi bi-question-circle"></i> {{ __('messages.nav.help') }}
                        </a>
                    </li>
            </ul>
            
            @auth
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordSneezeModal">
                        <i class="bi bi-plus-circle"></i> {{ __('messages.nav.record_sneeze') }}
                    </button>
                </div>
            @endauth
        </div>
    </div>
</nav>