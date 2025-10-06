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
				{{-- Users Tab --}}
				<x-admin.dashboard.tab-pane id="users" :active="true">

					<x-shared.search-bar heading="მომხმარებლები" headingPosition="left" />
					@if ($users->isEmpty())
						<x-ui.empty-state-message minHeight="30dvh" :resourceName="null" :overlay="false" />
					@endif
					<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 m-0">
						@foreach($users as $user)
							<x-admin.dashboard.explorer.entity-card :id="$user->id" type="user" :title="$user->full_name"
								:subtitle="($user->role->display_name ?? 'No Role') . ' • ' . ($user->phone ?? $user->email ?? '-')" />
						@endforeach
					</div>
					<!-- Modal Data is populated via JS -->
					<x-modal id="userModal" title="მომხმარებელის დეტალები" size="lg" height="min-content">
						<div class="position-relative" style="min-height: 400px; background: #f8f9fa;">
							<x-ui.spinner id="userModalSpinner" wrapperClass="d-none" centered size="lg" />
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