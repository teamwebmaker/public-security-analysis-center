@props([
	'id',
	'active' => false,
])

<div 
	class="tab-pane mt-2 fade {{ $active ? 'show active' : '' }}" 
	id="{{ $id }}-tab-content"
	role="tabpanel" 
	aria-labelledby="{{ $id }}-tab"
>
	<div {{ $attributes->merge(['class' => 'row']) }}>
		{{ $slot }}
	</div>
</div>
