<!doctype html>
<html lang="en">
<head>
 @include('partials.head')
</head>
<body data-languge="ka">
<div class="wrapper">
    @include('partials.header')
    @yield('main')
    @include('partials.footer')
</div>

<script  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script  src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script  src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script  src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script  src="{{ asset('scripts/app.js') }}"></script>
@yield('scripts')
</body>
</html>
