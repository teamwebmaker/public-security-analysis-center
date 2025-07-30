@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $user->full_name)


@php
	$rolesForSelect = $roles->pluck('display_name', 'id')->toArray();
	// Add initial selected role (empty at first)
	$selectedRole = old('role_id') ? $roles->firstWhere('id', old('role_id'))->name : null;
@endphp
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" action="{{ route($resourceName . '.update', $user) }}"
		title="მომხმარებლის რედაქტირება" :hasFileUpload="false" :backRoute="$resourceName . '.index'">

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="full_name" label="სრული სახელი" value="{{ old('full_name', $user->full_name) }}"
					placeholder="შეიყვანეთ სრული სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="role_id" :options="$rolesForSelect" :selected="old('role_id', $user->role_id)"
					label="როლი" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input type="tel" name="phone" label="მობილური" value="{{ old('phone', $user->phone) }}"
					placeholder="შეიყვანეთ მობილურის ნომეირ " autocomplete="tel" />
			</div>

			<div class="col-md-6 mb-3">
				<x-form.input type="email" name="email" label="ელ.ფოსტა" value="{{ old('email', $user->email) }}"
					placeholder="შეიყვანეთ ელ.ფოსტა" :required="false" autocomplete="email" />
			</div>
		</div>
		<div class="row mb-4">
			<div class="col-md-6">
				<x-form.input type="password" name="password" label="პაროლი" placeholder="შეიყვანეთ ახალი პაროლი"
					:required="false" />
				<div class="form-text">დატოვეთ ცარიელი, თუ არ გსურთ შეცვლა</div>
			</div>
			<div class="col-md-6">
				<x-form.input type="password" name="password_confirmation" label="პაროლის დადასტურება"
					placeholder="გაიმეორეთ ახალი პაროლი" :required="false" />
			</div>
		</div>

		<!-- Company Admin Section -->
		<div class="col-md-5 role-dependent" data-role="company_leader" style="display: none;">
			<x-form.checkbox-dropdown label="კომპანიები" :items="$companies" name="company_ids" labelField="name"
				:selected="old('company_ids', $user->companies->pluck('id')->toArray())" />
		</div>

		<!-- Responsible Person Section -->
		<div class="col-md-5 role-dependent" data-role="responsible_person" style="display: none;">
			{{-- <x-form.checkbox-dropdown label="ფილიალები" :items="$branches" name="branch_ids" labelField="name"
				:selected="old('branch_ids', $user->branches->pluck('id')->toArray())	" /> --}}
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