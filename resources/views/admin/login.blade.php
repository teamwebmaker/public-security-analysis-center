<!doctype html>
<html lang="en">

<head>
    @extends('partials.head')
    @section('title', 'ადმინის ავტორიზაცია')
</head>

<body data-language="en" style="font-family: 'Nunito', sans-serif">
    <!-- Display global errors -->
    @if ($errors->any())
        <x-ui.toast :messages="$errors->all()" type="error" />
    @endif
    <div class="container-fluid"
        style="background: linear-gradient(135deg, rgba(239, 246, 255, 1) 0%, rgba(224, 231, 255, 1) 100%);">
        <div class="container d-flex align-items-center justify-content-center   min-vh-100
bg-opacity-10  ">
            <x-auth.auth-form route="admin.auth" type="login">
                <div class="mb-3">
                    <x-form.input type="email" name="email" label="ელ.ფოსტა" :displayError="false"
                        placeholder="შეიყვანეთ ელ.ფოსტა" />
                </div>
                <div class="mb-4">
                    <x-form.input type="password" name="password" label="პაროლი" :displayError="false"
                        placeholder="შეიყვანეთ პაროლი" />
                </div>
            </x-auth.auth-form>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {!! load_script('scripts/bootstrap/bootstrapValidation.js') !!}
</body>

</html>