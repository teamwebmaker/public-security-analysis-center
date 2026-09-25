@extends('layouts.admin.admin-dashboard')
@section('title', 'Admin Dashboard')

@section('main')
	<x-ui.tabs :tabs="[
			['id' => 'info', 'label' => 'ინფორმაცია'],
			['id' => 'web_content', 'label' => 'ვებგვერდის კონტენტი'],
			['id' => 'management', 'label' => 'მენეჯმენტი'],
			['id' => 'resources', 'label' => 'რესურსები'],
		]">

		<!-- New custom tab -->
		<x-admin.dashboard.tab-pane id="info" :active="true">
			<div class="p-3">
				{{-- Users Tab --}}
				<x-admin.dashboard.tab-pane id="users" :active="true">

					<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
						<div>
							<h2 class="h5 mb-1">მომხმარებლები</h2>
							<p class="small text-muted mb-0">ნაჩვენებია 10-ამდე მომხმარებელი — სულ: {{ $userTotal }}</p>
						</div>
						<form method="GET" action="{{ route('admin.dashboard.page') }}" class="d-flex flex-column flex-sm-row gap-2">
							<input type="search" name="filter[search]" value="{{ request('filter.search') }}"
								class="form-control" placeholder="ძიება..." aria-label="მომხმარებლის ძიება">
							<select name="filter[role_id]" class="form-select" aria-label="როლის გაფილტვრა">
								<option value="">ყველა როლი</option>
								@foreach ($userRoles as $roleId => $roleName)
									<option value="{{ $roleId }}" @selected((string) request('filter.role_id') === (string) $roleId)>
										{{ $roleName }}
									</option>
								@endforeach
							</select>
							<button type="submit" class="btn btn-primary">ძიება</button>
							<a href="{{ route('admin.dashboard.page') }}" class="btn btn-outline-secondary">გასუფთავება</a>
							<a href="{{ route('users.index') }}" class="btn btn-outline-primary text-nowrap">ყველა მომხმარებელი</a>
						</form>
					</div>
					@if ($users->isEmpty())
						<x-ui.empty-state-message minHeight="30dvh" :resourceName="null" :overlay="false" />
					@endif
					<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 m-0">
						@foreach($users as $user)
							<x-admin.dashboard.explorer.entity-card :id="$user->id" type="user" :title="$user->full_name"
								:subtitle="($user->role->display_name ?? 'როლი უცნობია') . ' • ' . ($user->phone ?? $user->email ?? '-')"
								:role="$user->role?->name" :active="(bool) $user->is_active"
								:summary-url="route('users.dashboard-summary', $user)" />
						@endforeach
					</div>
					<!-- Modal Data is populated via JS -->
					<x-modal id="userModal" title="მომხმარებელის დეტალები" size="lg" height="80dvh">
						<div id="userModalContent" class="position-relative p-3" style="min-height: 400px; background: #f8f9fa;">
							<div class="d-flex justify-content-center align-items-center h-100" role="status">
								<div class="spinner-border text-primary" aria-hidden="true"></div>
								<span class="visually-hidden">იტვირთება...</span>
							</div>
						</div>
					</x-modal>

				</x-admin.dashboard.tab-pane>
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
