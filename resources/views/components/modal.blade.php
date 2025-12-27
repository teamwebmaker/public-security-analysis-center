<div {{ $attributes->merge(['class' => 'modal fade']) }} id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label">
    <div class="modal-dialog modal-{{ $size ?? 'md' }} modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                @isset($title)
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                @endisset
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0"
                style="height: {{ $height ?? '80dvh' }}; min-height: {{ $height ?? '80dvh' }}; overflow-y: scroll;">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
