@extends('layouts.admin.admin-dashboard')
@section('title', 'Admin Dashboard')

@section('main')
	<x-ui.tabs :tabs="[
			['id' => 'info', 'label' => 'ინფორმაცია'],
			['id' => 'web_content', 'label' => 'ვებგვერდის კონტენტი'],
			['id' => 'management', 'label' => 'მენეჯმენტი'],
		]">

		<!-- New custom tab -->
		<x-admin.dashboard.tab-pane id="info" :active="true">
			<div class="p-3">
				<x-ui.tabs :tabs="[
			['id' => 'users', 'label' => 'მომხმარებელი'],
			['id' => 'companies', 'label' => 'კომპანიები'],
			['id' => 'branches', 'label' => 'ფილიალები'],
			['id' => 'tasks', 'label' => 'სამუშაოები'],
		]"
					navClass="nav nav-pills gap-2 justify-content-center justify-content-md-start"
					buttonClass="btn btn-outline-primary" contentClass="tab-content p-1 p-sm-3 p-md-4 bg-light rounded">

					{{-- Users Tab --}}
					<x-admin.dashboard.tab-pane id="users" :active="true">
						<x-admin.dashboard.explorer.search-entity placeholder="Search users..." />

						<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
							@foreach($users as $user)
								<x-admin.dashboard.explorer.entity-card :id="$user->id" type="user" :title="$user->full_name"
									:subtitle="$user->phone ?? $user->email" />
							@endforeach
						</div>

						<x-modal id="userModal" title="მომხმარებელის დეტალები" size="lg" height="min-content">
							<div class="position-relative" style="min-height: 400px; background: #f8f9fa;">
								<x-ui.spinner id="userModalSpinner" wrapperClass="d-none" centered size="lg" />
							</div>
						</x-modal>

					</x-admin.dashboard.tab-pane>

					{{-- Other tabs --}}
				</x-ui.tabs>

			</div>
		</x-admin.dashboard.tab-pane>

		<!-- Dynamic tabs from $dashboardData -->
		@foreach($dashboardData as $groupKey => $resourceGroups)
			<x-admin.dashboard.tab-pane :id="$groupKey">
				@foreach($resourceGroups as $resourceGroup)
					<x-admin.dashboard.resource-group :resourceGroup="$resourceGroup" />
				@endforeach
			</x-admin.dashboard.tab-pane>
		@endforeach

	</x-ui.tabs>
@endsection

@section('scripts')
	{!! load_script('scripts/admin/info/index.js') !!}
@endsection

{{-- Companies Tab --}}
{{-- <x-admin.dashboard.tab-pane id="companies">
	<x-admin.dashboard.explorer.search-entity placeholder=" Search companies..." />

	<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
		@foreach($companies as $company)
		<x-admin.dashboard.explorer.entity-card type="company" :title="$company->name"
			:subtitle="$company->identification_code" @click="openCompanyModal({{ $company->id }})" />
		@endforeach
	</div>

	<x-admin.dashboard.explorer.entity-modal id="companyModal" title="Company Details" />
</x-admin.dashboard.tab-pane> --}}

{{-- Branches Tab --}}
{{-- <x-admin.dashboard.tab-pane id="branches">
	<x-admin.dashboard.explorer.search-entity placeholder="Search branches..." />
</x-admin.dashboard.tab-pane> --}}

{{-- Tasks Tab --}}
{{-- <x-admin.dashboard.tab-pane id="tasks">
	<x-admin.dashboard.explorer.search-entity placeholder="Search tasks..." />
</x-admin.dashboard.tab-pane> --}}