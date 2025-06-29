<!-- Floating Speed Dial Wrapper -->
<div class="position-fixed"
    style="bottom: 1rem; right: 1rem; display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">

    @if ($isCreate)
        <!-- Create Icon -->
        <a href="{{ route($resourceName . '.create') }}" class="btn btn-primary btn-lg rounded-circle shadow"
            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-plus-lg fs-4"></i>
        </a>
    @else
        <!-- Home Icon -->
        <a href="{{ route($resourceName . '.index') }}" class="btn btn-primary btn-lg rounded-circle shadow"
            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-house fs-4"></i>
        </a>
    @endif
</div>