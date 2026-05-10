@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}
        @if($required)<span class="text-danger">*</span>@endif
    </label>
@endif

@once
    <script>
        window.textareaField = function (options) {
            return {
                text: options.initial ?? '',
                max: options.max,
                min: options.min,
                editor: options.editor,
                showPreview: false,

                get length() {
                    return this.text.length;
                },

                get isAtLimit() {
                    return this.max && this.length > this.max;
                },

                get isBelowMin() {
                    return this.min !== null && this.length < this.min;
                },

                get badgeClass() {
                    if (this.isAtLimit) return 'text-danger fw-bold';
                    if (this.isBelowMin) return 'text-warning fw-semibold';
                    return 'text-success';
                },

                get hasLimit() {
                    return this.max;
                },

                get previewHtml() {
                    return this.render(this.text);
                },

                applyFormat(marker) {
                    const textarea = this.$refs.textarea;
                    const start = textarea.selectionStart ?? this.text.length;
                    const end = textarea.selectionEnd ?? this.text.length;
                    const selected = this.text.slice(start, end);
                    const placeholder = marker === '**' ? 'bold' : 'italic';
                    const innerText = selected || placeholder;
                    const formatted = `${marker}${innerText}${marker}`;

                    this.text = this.text.slice(0, start) + formatted + this.text.slice(end);

                    this.$nextTick(() => {
                        textarea.focus();
                        const selectionStart = start + marker.length;
                        const selectionEnd = selectionStart + innerText.length;
                        textarea.setSelectionRange(selectionStart, selectionEnd);
                    });
                },

                escapeHtml(value) {
                    return String(value ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                },

                render(value) {
                    let html = this.escapeHtml(value);

                    html = html.replace(/\*\*([^*]+?)\*\*/g, '<strong class="editor-content-bold">$1</strong>');
                    html = html.replace(/(^|[^*])\*([^*\n]+?)\*(?!\*)/g, '$1<em class="editor-content-italic">$2</em>');

                    return html.replace(/\r\n|\r|\n/g, '<br>');
                }
            };
        };
    </script>
@endonce

<!-- Textarea with dynamic character counter and validation indicator -->
<div x-data="textareaField({
    initial: @js(old($name, $value) ?? ''),
    max: {{ $maxlength ? $maxlength : 'null' }},
    min: {{ $minlength !== null ? ($minlength == 0 ? 1 : $minlength) : 'null' }},
    editor: @js($editor)
})">

    @if($editor)
        <div class="btn-toolbar gap-1 mb-2" role="toolbar" aria-label="{{ $label ?? $name }} formatting toolbar">
            <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center"
                title="Bold" aria-label="Bold" @click="applyFormat('**')">
                <i class="bi bi-type-bold"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center"
                title="Italic" aria-label="Italic" @click="applyFormat('*')">
                <i class="bi bi-type-italic"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center"
                :class="{ 'active': showPreview }" title="Preview" aria-label="Preview" @click="showPreview = !showPreview">
                <i class="bi" :class="showPreview ? 'bi-eye-slash' : 'bi-eye'"></i>
            </button>
        </div>
    @endif

    <div class="position-relative">
        <textarea id="{{ $id }}" name="{{ $name }}" x-ref="textarea" x-model="text" :class="{ 'border-danger text-danger': isAtLimit }"
            class="pb-3 {{ trim($class) }} form-control @error($name) is-invalid @enderror" @if($minlength !== null)
            minlength="{{ $minlength }}" @endif @if($maxlength) maxlength="{{ $maxlength + 1 }}" @endif @if($placeholder)
            placeholder="{{ $placeholder }}" @endif @if($required) required @endif rows="{{ $rows }}" {{ $attributes }}>
        </textarea>

        <!-- Live character counter badge -->
        <div class="badge rounded-pill bg-white border border-muted "
            style="position: absolute; bottom: -11px; right: 17px; pointer-events: none; ">
            <span :class="badgeClass"
                x-html="hasLimit ? `${length} / ${max}` : `${length} / <i class='bi bi-infinity fs-7'></i>`">
            </span>
        </div>
    </div>

    @if($editor)
        <div x-show="showPreview" x-cloak class="editor-content border rounded bg-light mt-3 px-3 py-2 text-body"
            style="min-height: 48px; white-space: normal;"
            x-html="previewHtml"></div>
    @endif

    @error($name)
        <div class="invalid-feedback d-inline-block">{{ $message }}</div>
    @enderror
</div>
