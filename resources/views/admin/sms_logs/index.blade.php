@extends('layouts.admin.admin-dashboard')

@section('title', 'SMS ისტორია')

<x-admin.index-view :items="$sms_logs" :resourceName="$resourceName" containerClass="position-relative">
	<div class="d-flex flex-column flex-lg-row align-items-center mb-3 border-bottom">
		<div class="flex-fill">
			<x-shared.filter-bar :filters="$filters" :showBadges="false" :resetUrl="route($resourceName . '.index')" />
		</div>
		<div class="flex-fill flex-lg-grow-0">
			<x-shared.search-bar headingPosition="left" :action="route($resourceName . '.index')" formClass="mb-0" />
		</div>
	</div>

	@if (!$sms_logs->isEmpty())
		<x-shared.table :items="$sms_logs" :headers="$smsLogHeaders" :rows="$smsLogRows" :actions="true"
			:sortableMap="$sortableMap" :resourceName="$resourceName" :tooltipColumns="['content', 'provider_message_id']" />
	@endif

	<x-modal id="sms-log-response-modal" title="პროვაიდერის პასუხი" size="lg">
		<h2 class="fs-5 m-3 me-0">
			მესიჯი:
			<span class="text-muted" id="sms-log-response-message-id">—</span>
		</h2>

		<pre class="bg-light border rounded mx-2 me-0 mb-0 p-1 small"><code class="text-dark"
				id="sms-log-response-content"></code></pre>
	</x-modal>
</x-admin.index-view>
@section('scripts')
	{!! load_script('scripts/sms/index.js') !!}
@endsection