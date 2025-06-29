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
    <x-admin.speed-dial :resourceName="$resourceName" :isCreate="true" />
@endsection