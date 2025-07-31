@extends('layouts.admin.admin-dashboard')
@section('title', 'მომხმარებლის შექმნა')
@section('main')

	@php
		$rolesForSelect = $roles->pluck('display_name', 'id')->toArray();
		// Add initial selected role (empty at first)
		$selectedRole = old('role_id') ? $roles->firstWhere('id', old('role_id'))->name : null;
	@endphp
	<x-admin.crud.form-container method="POST" title="მომხმარებლის შექმნა" action="{{ route($resourceName . '.store') }}"
		:hasFileUpload="false" :backRoute="$resourceName . '.index'">

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="full_name" label="სრული სახელი" value="{{ old('full_name') }}"
					placeholder="შეიყვანეთ სრული სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="role_id" :options="$rolesForSelect" :selected="old('role_id')" label="როლი" />
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
		<div class="row mb-4">
			<div class="col-md-6 mb-3">
				<x-form.input type="password" name="password" label="პაროლი" placeholder="შეიყვანეთ პაროლი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input type="password" name="password_confirmation" label="პაროლის დადასტურება"
					value="{{ old('password') }}" placeholder="გაიმეორეთ პაროლი" />
			</div>
		</div>

		<!-- Company Admin Section -->
		<div class="col-md-5 role-dependent" data-role="company_leader" style="display: none;">
			<x-form.checkbox-dropdown label="კომპანიები" :items="$companies" name="company_ids" labelField="name"
				:selected="old('company_ids')" />
		</div>

		<!-- Responsible Person Section  -->
		<div class="row">
			<div class="col-md-6 mb-3 role-dependent" data-role="responsible_person" style="display: none;">
				<x-form.checkbox-dropdown label="ფილიალები" :items="$branches" name="branch_ids" labelField="name"
					:selected="old('branch_ids')" />
			</div>
			<div class="col-md-6 mb-3 role-dependent" data-role="responsible_person" style="display: none;">
				<x-form.checkbox-dropdown label="სერვისებიზე წვდომა" :items="$services" name="service_ids"
					labelField="title.ka" :selected="old('service_ids')" />
			</div>
		</div>

		<!-- Worker Section -->
		<div class="col-md-5 mb-3 role-dependent" data-role="worker" style="display: none;">
			<x-form.checkbox-dropdown label="სამუშაოები" :items="$tasks" name="task_ids" labelField="name"
				:selected="old('task_ids')" />
		</div>
	</x-admin.crud.form-container>
@endsection

@section('scripts')
	<script>
		// pass roles to JS
		window.roles = @json($roles->pluck('name', 'id'));
	</script>
	{!! load_script('scripts/user/userCreate.js') !!}
@endsection