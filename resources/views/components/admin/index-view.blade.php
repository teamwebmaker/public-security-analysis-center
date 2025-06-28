@section('main')
    @session('success')
        <div class="alert alert-success" role="alert" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 3000)">
            {{ $value }}
        </div>
    @endsession

    <div class="{{ $containerClass }}">
        {{ $slot }}
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! $items->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    </div>

    <!-- Floating Speed Dial Button -->
    <a href="{{ route($resourceName . '.create') }}" class="btn btn-primary btn-lg rounded-circle shadow position-fixed"
        style="bottom: 1rem; right: 1rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-plus-lg fs-4"></i>
    </a>
@endsection