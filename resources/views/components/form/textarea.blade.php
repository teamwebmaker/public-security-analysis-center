@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}
        @if($required)<span class="text-danger">*</span>@endif
    </label>
@endif

<!-- Textarea with dynamic character counter and validation indicator -->
<div class="position-relative" x-data="{
    text: '{{ old($name, $value) }}',
    // Treat null as no limit (in maxlength case 0 is also treated as no limit) 
    // Treat min = 0 as min = 1 for UX clarity (In minlength case)
    max: {{ $maxlength ? $maxlength : 'null' }},
    min: {{ $minlength !== null ? ($minlength == 0 ? 1 : $minlength) : 'null' }},
    
    get length() {
        return this.text.length;
    },

    // Only flag as 'at limit' (red) if user exceeds maxlength
    get isAtLimit() {
        return this.max && this.length > this.max; // becomes red only if OVER max
    },

    // Show warning if below minimum length
    get isBelowMin() {
        return this.min !== null && this.length < this.min;
    },

    // Dynamically determine badge color based on validation
    get badgeClass() {
        if (this.isAtLimit) return 'text-danger fw-bold';
        if (this.isBelowMin) return 'text-warning fw-semibold';
        return 'text-success';
    },

    // Used to determine if counter should display max limit
    get hasLimit() {
        return this.max;
    }
    }">

    <textarea id="{{ $id }}" name="{{ $name }}" x-model="text" :class="{ 'border-danger text-danger': isAtLimit }"
        class="pb-3 {{ trim($class) }} form-control @error($name) is-invalid @enderror" @if($minlength !== null)
        minlength="{{ $minlength }}" @endif @if($maxlength) maxlength="{{ $maxlength + 1 }}" @endif @if($placeholder)
        placeholder="{{ $placeholder }}" @endif @if($required) required @endif rows="{{ $rows }}" {{ $attributes }}>
    </textarea>

    <!-- Live character counter badge -->
    <div class="badge rounded-pill bg-white border border-muted "
        style="position: absolute; bottom: -11px; right: 17px; pointer-events: none; ">
        <span :class="badgeClass" x-html="hasLimit ? `${length} / ${max}` : `${length} / <i class='bi bi-infinity fs-7'></i>`">
        </span>
        <template x-if="hasLimit">
            <span class="text-muted"> chars</span>
        </template>
    </div>

    @error($name)
        <div class="invalid-feedback d-inline-block">{{ $message }}</div>
    @enderror
</div>