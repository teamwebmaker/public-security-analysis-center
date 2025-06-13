<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.head')
    <link rel="stylesheet" href="{{ asset('styles/dashboard.css') . '?date=' . $modified }}">
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar shadow">
            <div class="sidebar-brand d-flex align-items-center justify-content-between justify-content-md-center p-3 ">
                <span class="fs-5 fw-bold">სამართავი პანელი</span>
                <button class="btn-close d-md-none" aria-label="Close"></button>
            </div>
            <div class="pt-3">
                @include('partials.accordion')
            </div>
        </div>

        {{-- top bar --}}
        <div class="topbar-content">
            <!-- Topbar -->
            <nav class="topbar d-flex align-items-center justify-content-between p-1  shadow-sm ">
                <button class="btn sidebar-toggler d-md-none" style="display: none;">
                    <i class="bi bi-list fs-3 "></i>
                </button>

                <div class="d-flex align-items-center w-100 justify-content-between px-2 px-sm-4">

                    <div>
                        <a href="{{ route('admin.dashboard.page') }}" class=" user-profile-btn text-decoration-none">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span>psac@admin.panel</span>
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn logout-btn d-flex gap-2 align-items-center ">
                            <i class="bi bi-box-arrow-right fs-5 "></i>
                            <span class="fs-8  d-none d-sm-block">გასვლა</span>
                        </button>
                    </form>
                </div>

            </nav>

            <!-- Content -->
            <div class="container-fluid py-4">
                <div class="container">
                    @yield('main')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script type="module" src="{{ asset('scripts/dashboard.js') . '?date=' . $modified }}"></script>
    @yield('scripts')
</body>

</html>