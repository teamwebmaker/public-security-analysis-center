<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="{{ __('static.meta.description') }}" />
<meta name="keywords" content="{{ __('static.meta.keywords') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

{{-- Prevents browser caching by appending last modified time --}}
{!! load_style('styles/app.css') !!}

<link rel="shortcut icon" href="{{ asset('images/themes/icon.png') }}" />
@yield('styles')
<title>@yield('title')</title>