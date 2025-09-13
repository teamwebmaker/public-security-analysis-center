<div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <div class="text-center mb-4">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center pe-1 m-auto mb-2"
            style="width: 50px; height: 50px;">
            <i class="bi bi-box-arrow-in-right fs-4"></i>
        </div>
        <h3 class="fw-bold"> {{$type == 'login' ? __('static.login.title') : __('static.signup.title')}}</h3>
    </div>
    <form method="POST" action="{{ route($route) }}" class="needs-validation" novalidate>
        @csrf
        {{ $slot }}
        <button type="submit"
            class="btn btn-primary w-100">{{$type == 'login' ? __('static.login.title') : __('static.signup.title')}}</button>
    </form>
    <!-- Optional extra buttons -->
    @isset($buttons)
        @if($separator !== 'none')
            <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                @if($separator === 'or')
                    <hr class="flex-grow-1">
                    ან
                    <hr class="flex-grow-1">
                @elseif($separator === 'line')
                    <hr class="flex-grow-1">
                @endif
            </div>
        @endif

        <div class="d-grid gap-2 {{ $separator === 'none' ? 'mt-3' : '' }}">
            {{ $buttons }}
        </div>
    @endisset

</div>