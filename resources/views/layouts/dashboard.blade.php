<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.head')
    {!! load_style('styles/dashboard.css') !!}
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar shadow">
            <div class="sidebar-brand d-flex align-items-center justify-content-between justify-content-md-center p-3 ">
                <span class="fs-5 fw-bold" style="cursor: default;">სამართავი პანელი</span>
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
                            {{-- <i class="bi bi-columns fs-5"></i> --}}
                            <i class="bi bi-columns-gap fs-5" style="rotate: 90deg;"></i>
                            <span class="d-none d-sm-block">მთავარი</span>
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Contacts  -->
                        <a href="{{ route('contacts.index') }}" class="btn logout-btn">
                            <i class="bi bi-mailbox2-flag fs-4"></i>
                        </a>
                        <!-- subscriptions  -->
                        <a href="{{ route('push.index') }}" class="btn logout-btn">
                            <i class="bi bi-bell-fill fs-4"></i>
                        </a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="btn logout-btn d-flex gap-2 align-items-center ">
                                <i class="bi bi-box-arrow-right fs-5 "></i>
                                <span class="fs-8 d-none d-sm-block">გასვლა</span>
                            </button>
                        </form>
                    </div>

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
    {!! load_script('scripts/bootstrap/bootstrapTooltips.js') !!}
    {!! load_script('scripts/admin/dashboard.js') !!}
    {!! load_script('scripts/admin/index.js') !!}
    @yield('scripts')
</body>

</html>