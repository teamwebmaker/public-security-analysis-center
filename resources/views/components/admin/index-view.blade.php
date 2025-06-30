@section('main')

    <!-- Global Success Display -->
    @if(session()->has('success'))
        <x-ui.toast :messages="[session('success')]" type="success" />
    @endif

    <!-- Global Error Display -->
    @if ($errors->any())
        <x-ui.toast :messages="$errors->all()" type="error" />
    @endif

    <div class="{{ $containerClass }}">
        {{ $slot }}
    </div>

    <!-- Empty State Placeholder -->
    @if ($items->isEmpty())
        <div class="position-absolute top-50 start-50 translate-middle text-center text-muted">
            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
            <p class="mb-1">დოკუმენტები ვერ მოიძებნა</p>
            <a href="{{ route($resourceName . '.create') }}"
                class="text-primary small text-decoration-none d-inline-flex align-items-center">
                დამატება
                <i class="bi bi-plus  fs-5"></i>
            </a>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            {!! $items->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    </div>

    <!-- Floating Speed Dial Button -->
    <x-admin.speed-dial :resourceName="$resourceName" :isCreate="true" />
@endsection