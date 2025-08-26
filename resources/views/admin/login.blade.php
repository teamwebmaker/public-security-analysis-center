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
	<title>ადმინისტრატორის ავტორიზაცია</title>
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
			<x-auth.auth-form route="admin.auth" type="login" separator="or">
				<div class="mb-3">
					<x-form.input type="email" name="email" label="ელ.ფოსტა" :displayError="false"
						placeholder="შეიყვანეთ ელ.ფოსტა" autocomplete="email" value="{{ old('email') }}" />
				</div>
				<div class="mb-4">
					<x-form.input type="password" name="password" label="პაროლი" :displayError="false"
						placeholder="შეიყვანეთ პაროლი" />
				</div>
				<x-slot name="buttons">
					<a href="{{ url('/') }}" class="btn btn-outline-primary w-100">
						<i class="bi bi-house-door"></i> მთავარი გვერდი
					</a>
				</x-slot>
			</x-auth.auth-form>
		</div>
	</div>



	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	{!! load_script('scripts/bootstrap/bootstrapValidation.js') !!}
</body>

</html>