@extends('layouts.admin.admin-dashboard')
@section('title', 'Admin Dashboard')
@section('main')
	<x-ui.tabs contentContainerClass="tab-content" :tabs="[
			['id' => 'web_content', 'label' => 'ვებგვერდის კონტენტი'],
			['id' => 'management', 'label' => 'მენეჯმენტი'],
		]">
		@foreach($dashboardData as $groupKey => $resourceGroups)
			<div class="tab-pane mt-2 fade {{ $loop->first ? 'show active' : '' }}" id="{{ $groupKey }}-tab-content"
				role="tabpanel" aria-labelledby="{{ $groupKey }}-tab">
				<div class="row">
					@foreach($resourceGroups as $resourceGroup)
						<div class="col">
							<div class="card border border-white mb-2 rounded-4 bg-light p-3">
								@if(!empty($resourceGroup->icon) || !empty($resourceGroup->label))
									<h6 class="text-muted d-flex align-items-center gap-2">
										@isset($resourceGroup->icon)
											<i class="bi {{ $resourceGroup->icon }} text-secondary"></i>
										@endisset
										{{ $resourceGroup->label ?? '' }}
									</h6>
								@endif
								<div class="d-flex flex-row flex-wrap gap-3">
									@foreach($resourceGroup->resources as $resource)
										<div class="col" style="min-width: 230px">
											<x-admin.dashboard.dashboard-card :icon="$resource->icon" :title="$resource->title"
												:count="$resource->count" :viewRoute="$resource->viewRoute ?? null"
												:createRoute="$resource->createRoute ?? null" />
										</div>
									@endforeach
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		@endforeach
	</x-ui.tabs>
@endsection