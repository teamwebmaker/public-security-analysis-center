@php
    $uid = 'dropdown-' . uniqid();
    $optionList = collect($options);
@endphp

@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}@if($required)&#xa0;<span class="text-danger">*</span> @endif
    </label>
@endif

<div x-data="selectDropdown('{{ $uid }}', {{ $optionList->count() >= 6 ? 'true' : 'false' }}, '{{ (string) $selected }}', {{ $required ? 'true' : 'false' }})"
    x-init="init()" data-options='@json($optionList)' class="position-relative">

    <input type="hidden" id="{{ $id }}" name="{{ $name }}" :value="selected">

    <div class="form-select" x-on:click="toggle()" x-text="selectedText || 'აირჩიეთ ვარიანტი'"
        :class="{ 'is-invalid': @json($errors->has($name)) }" style="cursor: pointer;"></div>


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

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>


<script>

    function selectDropdown(id, searchable = false, preselectedValue = '', required = false) {
        return {
            open: false,
            selected: preselectedValue,
            selectedText: '',
            search: '',
            searchable: searchable,
            required: required,

            init() {
                // try to get text from the DOM (Blade side has rendered it already)
                const textMap = JSON.parse(this.$el.dataset.options || '{}');
                if (this.selected && textMap[this.selected]) {
                    this.selectedText = textMap[this.selected];
                }
            },

            toggle() {
                this.open = !this.open;
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