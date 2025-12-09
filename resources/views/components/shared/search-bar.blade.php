@props([
	// Functional props
	'action' => request()->url(),
	'placeholder' => 'ძიება...',
	'name' => 'filter[search]',
	'value' => request('filter.search'),
	'showLabel' => false,
	'heading' => '',
	'headingPosition' => 'left', // left | right

	// Styling props
	'formClass' => 'mb-2',
	'wrapperClass' => 'd-flex flex-wrap flex-column flex-sm-row align-items-center justify-content-between gap-3 p-1 ',
	'inputWrapperClass' => 'd-flex flex-column flex-sm-row align-items-center justify-content-center gap-2',
	'inputClass' => 'form-control w-auto',
	'labelClass' => 'position-absolute start-0 text-muted m-0 align-self-start pt-1 small',
	'buttonRowClass' => 'd-flex gap-2 justify-content-center',
	'searchButtonClass' => 'btn btn-primary',
	'clearButtonClass' => 'btn btn-danger',
])

@php
	$isLeft = $headingPosition === 'left';
@endphp
<form method="GET"
	id="searchForm-{{ md5($action . $name) }}"
	action="{{ $action }}"
	class="{{ $formClass }}"
	data-search-bar
	data-search-name="{{ $name }}">
	<div class="{{ $wrapperClass }} flex-sm-{{ $isLeft ? 'row' : 'row-reverse' }}">
		{{-- Heading --}}
		@if($heading)
			<div class="text-center text-sm-start">
				<p class="fw-bold fs-5 m-0">{{ $heading }}</p>
			</div>
		@endif

		{{-- Input + Buttons --}}
		<div class="{{ $inputWrapperClass }}">
			<div class="position-relative">
				<x-form.input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}"
					class="{{ $inputClass }}" autocomplete="off" :required="false" />

				@if($showLabel && $value)
					<p class="{{ $labelClass }}">
						საძიებო სიტყვა: <strong>{{ $value }}</strong>
					</p>
				@endif
			</div>

			<div class="{{ $buttonRowClass }}">
				<button type="submit" class="{{ $searchButtonClass }}">ძიება</button>
				<button type="button" class="{{ $clearButtonClass }} clear-search">
					<i class="bi bi-trash-fill"></i>
				</button>
			</div>
		</div>
	</div>
</form>

@once
	{!! load_script('scripts/components/search-bar.js') !!}
@endonce
