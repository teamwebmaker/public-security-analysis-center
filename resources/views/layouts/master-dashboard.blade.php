<!DOCTYPE html>
<html lang="ka">

<head>
    @include('partials.head')
    {!! load_style('styles/dashboard.css') !!}
    @stack('styles')
</head>

<body>
    <div class="dashboard-wrapper">
        {{-- Sidebar (to be defined in child layout) --}}
        <div class="sidebar shadow overflow-y-auto">
            @yield('sidebar')
        </div>

        {{-- Topbar & Content --}}
        <div class="topbar-content">
            {{-- Topbar --}}
            @yield('topbar')

            {{-- Main Page Content --}}
            <div class="container-fluid py-4">
                <div class="container">
                    @yield('main')
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {!! load_script('scripts/bootstrap/bootstrapTooltips.js') !!}
    @stack('scripts-before')
    {!! load_script('scripts/admin/dashboard.js') !!}
    {!! load_script('scripts/admin/index.js') !!}
    @stack('scripts')
</body>

</html>