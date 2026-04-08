<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @include('partials.head')
  @yield('head-custom')
</head>

<body data-language="{{ app()->getLocale() }}">
  <div class="wrapper">
    @include('partials.header')
    @yield('main')
    @include('partials.partners')
    @include('partials.footer')
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  {{-- Prevent browser cache by appending last modified date --}}
  {!! load_script('scripts/app.js') !!}
  @yield('scripts')
</body>

</html>
