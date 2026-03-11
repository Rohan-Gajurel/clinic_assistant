  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        {{-- <img src="{{ asset('assets/img/logo.webp') }}" alt="">  --}}
        <h1 class="sitename">Tele<span>Med</span></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ route('home') }}" class="active">Home</a></li>
          <li><a href="about.html">About</a></li>
          <li><a href="departments.html">Departments</a></li>
          <li><a href="services.html">Services</a></li>
          <li><a href="{{ route('frontend.doctors') }}">Doctors</a></li>
          <li><a href="contact.html">Contact</a></li>
            @if(auth()->check())
            <li class="dropdown">
              <a href="#" class="nav-link dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle" style="color: var(--primary-color); font-size: 1.25rem;"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown" style="border-radius: 0.75rem; min-width: 220px;">
                <li class="px-3 py-2 border-bottom">
                  <div>
                    <span class="d-block fw-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <small class="text-muted">{{ Auth::user()->email ?? 'admin@example.com' }}</small>
                  </div>
                </li>
                <li>
                  <a class="dropdown-item py-2" href="{{ route('frontend.appointments') }}">
                   Appointment History
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item py-2 text-danger">
                      <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                  </form>
                </li>
              </ul>
            </li>
            @else
            <li><a href="{{ route('login') }}">Login</a></li>
            @endif
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="{{ route('appointment') }}">Appointment</a>

    </div>
  </header>