<style>
    .toast-fade-in-out-success {
        animation: toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards,
            toastOut 0.4s cubic-bezier(0.7, 0, 0.84, 0) forwards 3.5s;
    }

    .toast-fade-in-out-error {
        animation: on_error_toastIn 0.4s forwards;
    }

    .pulse-ring-success {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #d0f2c7;
        animation: pulseRing 1.5s ease infinite;
    }

    .pulse-ring-error {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #fcd7d7;
        animation: pulseRing 1.5s ease infinite;
    }

    @keyframes toastIn {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes toastOut {
        0% {
            opacity: 1;
            transform: translateY(0);
        }

        100% {
            opacity: 0;
            transform: translateY(-10px);
        }
    }

    @keyframes on_error_toastIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    @keyframes pulseRing {
        0% {
            width: 20px;
            height: 20px;
            opacity: 0.8;
        }

        50% {
            width: 35px;
            height: 35px;
            opacity: 0.5;
        }

        100% {
            width: 20px;
            height: 20px;
            opacity: 0.8;
        }
    }
</style>

<div class="position-fixed top-0 start-0 end-0 px-3 m-2 mt-3 pb-3 d-flex flex-column gap-2" style="z-index: 1100;">
    @foreach ($messages as $message)
    <div x-data="{ show: true }" x-show="show" x-transition @if($type === 'success')
        x-init="setTimeout(() => show = false, 4000)" @endif class="toast {{ $type === 'success' ? 'toast-fade-in-out-success' : 'toast-fade-in-out-error' }}
                rounded-top-2 rounded-bottom-1 show w-100 mx-auto bg-white border-0 shadow-lg"
        style="max-width: 350px;" role="alert" aria-live="assertive" aria-atomic="true">

        <div class="d-flex align-items-center p-1 {{ $type === 'success' ? 'pb-0' : '' }}">
            <div class="d-flex align-items-center gap-1 flex-grow-1">
                <div class="position-relative" style="width: 40px; height: 40px; isolation: isolate;">
                    <div class="position-absolute top-50 start-50 translate-middle 
                            {{ $type === 'success' ? 'pulse-ring-success' : 'pulse-ring-error' }}">
                    </div>
                    @if($type === 'success')
                        <i class="bi bi-check-circle-fill fs-5 position-absolute top-50 start-50 translate-middle"
                            style="color: #61D345;"></i>
                    @else
                        <i class="bi bi-x-circle-fill fs-5 position-absolute top-50 start-50 translate-middle"
                            style="color: #dc3545;"></i>
                    @endif
                </div>
                <div class="toast-body p-0"
                    style="cursor: default; color: {{ $type === 'success' ? '#27541c' : '#842029' }};">
                    {{ $message }}
                </div>
            </div>

            <button type="button" class="btn-close btn-close-dark mx-2" @click="show = false"
                aria-label="Close"></button>
        </div>

        @if($type === 'success')
            <div class="progress rounded-0 rounded-bottom bg-white" style="height: 4px;">
                <div class="progress-bar" role="progressbar" x-init="setTimeout(() => $el.style.width = '0%', 50)"
                    style="width: 100%; transition: width 4s linear; background-color: #61D345;">
                </div>
            </div>
        @endif
    </div>
    @endforeach
</div>