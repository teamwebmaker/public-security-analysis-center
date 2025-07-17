@extends('layouts.dashboard')
@section('title', 'მომხმარებლის შექმნა')
@section('main')
	<x-admin.crud.form-container method="POST" title="მომხმარებლის შექმნა" action="{{ route($resourceName . '.store') }}"
		:hasFileUpload="false" :backRoute="$resourceName . '.index'">

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="full_name" label="სრული სახელი" value="{{ old('full_name') }}"
					placeholder="შეიყვანეთ სრული სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="role_id" :options="$roles" :selected="null" label="როლი" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input type="tel" name="phone" label="მობილური" value="{{ old('phone') }}"
					placeholder="შეიყვანეთ მობილურის ნომეირ " autocomplete="tel" />
			</div>

			<div class="col-md-6 mb-3">
				<x-form.input type="email" name="email" label="ელ.ფოსტა" value="{{ old('email') }}"
					placeholder="შეიყვანეთ ელ.ფოსტა" :required="false" autocomplete="email" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<x-form.input type="password" name="password" label="პაროლი" placeholder=" შეიყვანეთ პაროლი" />

			</div>
			<div class="col-md-6">
				<x-form.input type="password" name="password_confirmation" label="პაროლის დადასტურება"
					value="{{ old('password') }}" placeholder="გაიმეორეთ პაროლი" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection

@section('scripts')
	{!! load_script('scripts/imgUploadInit.js') !!}
@endsection