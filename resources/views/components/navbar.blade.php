<style>
    .navbar {
        background-color: #2C3E50;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        font-weight: 700;
        color: white;
        transition: color 0.3s;
    }

    .navbar-brand:hover {
        color: #FFC107;
    }

    .nav-link {
        color: white;
        margin-right: 15px;
        transition: color 0.3s;
    }

    .nav-link:hover {
        color: #FFC107;
    }

    .nav-link.active {

        color: #FFC107 !important;
    }

    .navbar-toggler {
        border: none;
    }

    .navbar-toggler:focus {
        box-shadow: none;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #343a40;
    }

    @media (max-width: 576px) {
        .nav-link {
            margin-right: 0;
            padding: 10px 15px;
        }
    }
</style>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">JobSphere</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.home') ? 'active' : '' }}"
                            href="{{ route('dashboard.home') }}">Home</a>
                    </li>

                    @if (auth()->user()->employer())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('job.post.listings') ? 'active' : '' }}"
                                href="{{ route('job.post.listings') }}">Manage Job Post</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('job.review') ? 'active' : '' }}"
                                href="{{ route('job.review') }}">Review
                                Job Application</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('job.own') ? 'active' : '' }}"
                                href="{{ route('job.own') }}">My Jobs
                                ({{ auth()->user()->jobs()->whereIn('job_status', [0, 1, 2])->count() }})
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('profile') ? 'active' : '' }}"
                            href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Hi, {{ auth()->user()->name }}
                            ({{ str()->upper(auth()->user()->user_role[auth()->user()->role]) }})
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('view.profile') }}">Profile</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('auth.logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@auth
    @if (auth()->user()->employer())
        @include('components.modals.jobs.post')
    @endif
@endauth
