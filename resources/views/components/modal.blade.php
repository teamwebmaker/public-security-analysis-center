<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-{{ $size }} modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                @if ($title)
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="height: 80vh;">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>