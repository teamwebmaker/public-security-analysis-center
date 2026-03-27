@extends('layouts.admin.admin-dashboard')

@section('title', 'SMS ისტორია')

<x-admin.index-view :items="$sms_logs" :resourceName="$resourceName" containerClass="position-relative">
	<div class="d-flex flex-column flex-lg-row align-items-center mb-3 border-bottom">
		<div class="flex-fill">
			<x-shared.filter-bar :filters="$filters" :showBadges="false" :resetUrl="route($resourceName . '.index')" />
		</div>
		<div class="px-2 pb-3 pb-lg-0">
			<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
				data-bs-target="#sms-log-sync-modal">
				ხელახლა ჩატვირთვა
			</button>
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

	<x-modal id="sms-log-sync-modal" title="SMS სტატუსების განახლება" size="md" height="min-content">
		<div class="p-3">
			<p class="mb-2">SMS ლოგების რაოდენობა: <strong>{{ $totalSmsLogs }}</strong></p>
			<p class="mb-2">აირჩიეთ რამდენი ბოლო ჩანაწერი გადავამოწმოთ:</p>

			<div class="d-flex flex-wrap gap-2 mb-3" id="sms-log-sync-options">
				<button type="button" class="btn btn-outline-secondary js-sms-sync-option" data-limit="10">ბოლო 10</button>
				<button type="button" class="btn btn-outline-secondary js-sms-sync-option" data-limit="20">ბოლო 20</button>
				<button type="button" class="btn btn-outline-secondary js-sms-sync-option" data-limit="50">ბოლო 50</button>
			</div>

			<div id="sms-log-sync-loader" class="d-none align-items-center gap-2 mb-2">
				<div class="spinner-border spinner-border-sm text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<span>მიმდინარეობს სტატუსების განახლება...</span>
			</div>

			<div id="sms-log-sync-error" class="small text-danger"></div>
		</div>
	</x-modal>
</x-admin.index-view>
@section('scripts')
	<script>
		window.APP_CSRF_TOKEN = "{{ csrf_token() }}";
		window.SMS_LOG_SYNC_ROUTE = "{{ route('sms_logs.sync-statuses') }}";
	</script>
	{!! load_script('scripts/sms/index.js') !!}
@endsection