@php
	$uid = 'dropdown-' . uniqid();
	$optionList = collect($options);
	$isEmpty = $optionList->isEmpty();
@endphp

@if($label)
	<label for="{{ $id }}" class="form-label">
		{{ $label }}@if($required)&#xa0;<span class="text-danger">*</span> @endif
	</label>
@endif

<div
	x-data="selectDropdown('{{ $uid }}', {{ $optionList->count() >= 6 ? 'true' : 'false' }}, '{{ (string) $selected }}', {{ $required ? 'true' : 'false' }}, {{ $isEmpty ? 'true' : 'false' }})"
	x-init="init()" data-options='@json($optionList)' class="position-relative">

	<input type="hidden" x-ref="hidden" id="{{ $id }}" name="{{ $name }}" :value="selected">

	<div class="form-select"
		:class="{ 'is-invalid': @json($errors->has($name)), 'disabled bg-light text-muted': isEmpty }"
		x-on:click="!isEmpty && toggle()"
		x-text="isEmpty ? '{{ $label }} ვერ მოიძებნა' : (selectedText || 'აირჩიეთ ვარიანტი')" style="cursor: pointer;">
	</div>

	<template x-if="!isEmpty">
		<div x-show="open" x-cloak @click.outside="close()"
			class="border mt-1 bg-white w-100 position-absolute rounded shadow-sm"
			style="max-height: 250px; overflow-y: auto; z-index: 100;" x-transition>

			<template x-if="searchable">
				<div class="p-2">
					<input type="text" x-model="search" class="form-control form-control-sm" placeholder="ძიება...">
				</div>
			</template>

			<ul class="list-group list-group-flush">
				@foreach($options as $value => $text)
					<li class="list-group-item list-group-item-action"
						x-show="search === '' || '{{ Str::lower($text) }}'.includes(search.toLowerCase())"
						x-on:click="select('{{ $value }}', '{{ $text }}')" style="cursor: pointer;">
						{{ $text }}
					</li>
				@endforeach
			</ul>
		</div>
	</template>

	@error($name)
		<div class="invalid-feedback d-block">
			{{ $message }}
		</div>
	@enderror
</div>


<script>
	function selectDropdown(id, searchable = false, preselectedValue = '', required = false, isEmpty = false) {
		return {
			open: false,
			selected: preselectedValue,
			selectedText: '',
			search: '',
			searchable: searchable,
			required: required,
			isEmpty: isEmpty,

			init() {
				if (this.isEmpty) return; // skip init if empty
				const textMap = JSON.parse(this.$el.dataset.options || '{}');
				if (this.selected && textMap[this.selected]) {
					this.selectedText = textMap[this.selected];
				}
			},

			toggle() {
				if (!this.isEmpty) {
					this.open = !this.open;
				}
			},

			close() {
				this.open = false;
			},

			select(value, text) {
				this.selected = value;
				this.selectedText = text;
				this.search = '';
				this.close();


			}

		}
	}
</script>