<!doctype html>
<html lang="en">

<head>
  @extends('partials.head')
  @section('title', 'Log in page')

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
              <x-form.input type="tel" name="phone" label="მობილური" :displayError="false"
                placeholder="შეიყვანეთ მობილურის ნომერი" autocomplete="tel" />
            </div>
            <div class="mb-4">
              <x-form.input type="password" name="password" label="პაროლი" :displayError="false"
                placeholder="შეიყვანეთ პაროლი" />
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