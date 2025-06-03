<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size ?? 'md' }} modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                @isset($title)
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                @endisset
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height: 80vh; overflow-y: auto;">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>