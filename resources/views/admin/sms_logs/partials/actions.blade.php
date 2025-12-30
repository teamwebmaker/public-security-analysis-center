@php
	$canResend = (bool) ($canResend ?? false);
	$hasProviderResponse = !empty($smsLog->provider_response);
	$buttonClass = $canResend ? 'btn-danger' : 'btn-outline-secondary';
	$buttonTitle = $canResend ? 'ხელახლა გაგზავნა' : 'ხელახლა გაგზავნა შეუძლებელია';
@endphp

<div class="d-flex gap-2 align-items-center justify-content-center">
	<form method="POST" action="{{ route('sms.send') }}"
		onsubmit="return confirm('დარწმუნებული ხართ რომ გსურთ მესიჯის თავიდან გაგზავნა?')" class="m-0">
		@csrf
		<input type="hidden" name="sms_log_id" value="{{ $smsLog->id }}" />
		<input type="hidden" name="smsno" value="{{ $smsLog->smsno }}" />
		<input type="hidden" name="destination" value="{{ $smsLog->destination }}" />
		<input type="hidden" name="content" value="{{ $smsLog->content }}" />
		<button type="submit" class="btn btn-sm {{ $buttonClass }}" title="{{ $buttonTitle }}"
			@disabled(!$canResend)>
			<i class="bi bi-arrow-repeat"></i>
		</button>
	</form>

	@if ($hasProviderResponse)
		<button type="button" class="btn btn-sm btn-outline-primary js-sms-response-trigger"
			data-bs-toggle="modal" data-bs-target="#sms-log-response-modal" data-response='@json($smsLog->provider_response)'
			data-message-id="{{ e($smsLog->provider_message_id ?? '') }}" title="პროვაიდერის პასუხი">
			<i class="bi bi-info-circle"></i>
		</button>
	@endif
</div>
