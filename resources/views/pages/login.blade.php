<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="{{ __('static.meta.description') }}" />
  <meta name="keywords" content="{{ __('static.meta.keywords') }}" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

  {!! load_style('styles/app.css') !!}

  <link rel="shortcut icon" href="{{ asset('images/themes/icon.png') }}" />
  <title>{{ __('static.pages.login.title') }}</title>
</head>

<body data-languge="ka">
  <!-- Display global errors -->
  @if ($errors->any())
    <x-ui.toast :messages="$errors->all()" type="error" />
  @endif
  <div class="wrapper">
    @include('partials.header')
    <main>
      <div class="container-fluid">
        <div class="container d-flex align-items-center justify-content-center bg-opacity-10 my-5"
          style="min-height: 40dvh;">
          <x-auth.auth-form route="login" type="login">
            <div class="mb-3">
              <x-form.input type="tel" name="phone" :value="old('phone')" :label="__('static.form.phone')"
                :displayError="false" :placeholder="__('static.form.placeholders.phone')" autocomplete="tel" />
            </div>
            <div class="mb-4">
              <x-form.input type="password" name="password" :label="__('static.form.password')" :displayError="false"
                :placeholder="__('static.form.placeholders.password')" />
            </div>
          </x-auth.auth-form>
        </div>
      </div>
    </main>
    @include('partials.footer')
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {!! load_script('scripts/bootstrap/bootstrapValidation.js') !!}
</body>

</html>