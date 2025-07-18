<!doctype html>
<html lang="en">

<head>
  @include('partials.head')
</head>

<body data-languge="ka">
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