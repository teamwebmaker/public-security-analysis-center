@extends('management.master')
@section('title', 'კომპანიის მონაცემები')

@section('main')
	<!-- Stats -->
	<div class="row g-3">
		<x-management.stat-card label="აქტიური სამუშაოები" :count="$inProgressTasks->count()" icon="bi bi-ui-radios"
			iconWrapperClasses="bg-warning bg-opacity-10 text-warning" />

		<x-management.stat-card label="კომპანიები" :count="$userCompanies->count()" icon="bi bi-building-fill"
			iconWrapperClasses="bg-primary bg-opacity-10 text-primary" />

		<x-management.stat-card label="ფილიალები" :count="$userCompanies->pluck('branches')->flatten()->count()"
			icon="bi bi-diagram-3-fill" iconWrapperClasses="bg-success bg-opacity-10 text-success" />

		<x-management.stat-card label="პასუხისმგებელი პირები" :count="$branchUsersCount" icon="bi bi-people-fill"
			iconWrapperClasses="text-purple bg-opacity-10" iconWrapperStyle="background-color: #f5eef7;" />
	</div>

	<!-- Tasks -->
	@if ($tasks->isNotEmpty())

		<div class="my-3 shadow-sm rounded-3 overflow-hidden ">
			<x-shared.table :items="$tasks" :headers="$taskHeaders" :rows="$taskRows" :sortableMap="$sortableMap"
				:tooltipColumns="['branch', 'service']" :actions="false" />
		</div>

	@endif

	<!-- company & branches-->
	<div class="accordion mt-3" id="companyAccordion">
		@foreach ($userCompanies as $index => $company)
			@php
				$accordionId = 'company-' . $company->id;
				$isOpen = $index === 0;
			@endphp

			<x-accordion-item :id="$accordionId" :parent="'companyAccordion'" :open="$isOpen" :label="$company->name . ' · ' . $company->economic_activity_type->name . ' · ' . $company->identification_code" icon="bi-building">

				<!-- Branch cards -->
				<div class="row g-4 m-0 mb-4">
					@forelse ($company->branches as $branch)
						<x-management.branch.card :branch="$branch" />
					@empty
						<p class="text-center">ფილიალები ვერ მოიძებნა</p>
					@endforelse
				</div>
				</x-ui.accordion-item>
		@endforeach
	</div>

	<!-- responsible users modal -->
	<x-modal id="responsible-users-modal" title="პასუხისმგებელი პირები" size="lg">
		@foreach ($userBranchMap as $data)
			@php
				$user = $data['user'];
				$branches = $data['branches'];
			@endphp
			<x-management.shared.user-card :name="$user->full_name" :email="$user->email" :number="$user->phone">
				<!-- user branches  -->
				<x-slot name="additionalData">
					@if (count($branches) === 1)
						<span class="badge bg-secondary">{{ $branches[0] }}</span>
					@else
						<div class="dropdown">
							<button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
								ფილიალები ({{ count($branches) }})
							</button>
							<ul class="dropdown-menu">
								@foreach ($branches as $branchName)
									<li class="mx-2 mt-1 rounded-2 bg-light "><span
											class="dropdown-item disabled text-dark">{{ $branchName }}</span>
									</li>
								@endforeach
							</ul>
						</div>
					@endif
				</x-slot>
			</x-management.shared.user-card>
		@endforeach

	</x-modal>
@endsection