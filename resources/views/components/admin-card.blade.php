<div class="col-xl-4 col-lg-6 mb-4">
    <div class="card h-100 border-0 shadow-sm overflow-hidden position-relative">
        <!-- Card Image -->
        <div class="card-header p-0 border-0 bg-transparent position-relative">
            <img class="img-fluid w-100" src="{{ asset($image) }}" alt="{{ $title }}"
                style="height: 180px; object-fit: cover;">
            <!-- Visibility Overlay -->
            @if($visibility == 0)
                <div class="position-absolute  top-0 start-0 w-100 h-100 bg-dark bg-opacity-75"></div>
            @endif
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <div class="d-flex zalign-items-start gap-2">
                <h3 class="card-title h5 mb-3 text-truncate flex-grow-1" title="{{ $title }}">
                    {{ $title }}
                </h3>
                <!-- Small visibility indicator -->
                <div>

                    @if($visibility == 0)
                        <i class="bi bi-eye-slash fs-5 text-danger mt-1"></i>
                    @else
                        <i class="bi bi-eye fs-5 text-success mt-1"></i>
                    @endif
                </div>

            </div>

            @if ($description)
                <p class="card-text text-muted line-clamp mb-4" style="--bs-line-clamp: 3;">
                    {{ $description }}
                </p>
            @endif
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
            <div class="d-flex justify-content-between gap-2">
                <a href="{{ $editUrl }}"
                    class="btn btn-outline-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit</span>
                </a>
                <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('წავშალოთ პროექტი?')"
                    class="flex-grow-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-trash"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>