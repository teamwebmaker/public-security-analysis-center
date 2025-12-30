@section('main')

    <!-- Global Success Display -->
    @if(session()->has('success'))
        <x-ui.toast :messages="[session('success')]" type="success" />
    @endif

    <!-- Global Error Display -->
    @if (session('error'))
        <x-ui.toast :messages="[session('error')]" type="error" />
    @endif

    <!-- Error Display for Validation -->
    @if ($errors->any())
        <x-ui.toast :messages="$errors->all()" type="error" />
    @endif


    <div class="{{ $containerClass }}">
        {{ $slot }}
    </div>

    <!-- Empty State Placeholder -->
    @if ($items->isEmpty())
        <x-ui.empty-state-message :overlay="false" />
    @endif

    <div class="row">
        <div class="col-md-12 pt-3">
            {!! $items->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    </div>

    <!-- Floating Speed Dial Button -->
    @if ($hasSpeedDial)
        <x-admin.speed-dial :resourceName="$resourceName" :isCreate="true" />
    @endif

@endsection