@php
    // get resourceName from back route for speed-dial
    $indexResourceName = explode('.', $backRoute)[0];
@endphp

<div class="row justify-content-center">
    <div class="{{ $cardWrapperClass }}">

        <!-- Global Success Display -->
        @if(session()->has('success'))
            <x-ui.toast :messages="[session('success')]" type="success" />
        @endif

        <!-- Global Error Display -->
        @if ($errors->any())
            <x-ui.toast :messages="$errors->all()" type="error" />
        @endif

        <div class="card {{ $cardClass }}">
            <!-- slot for visual header -->
            {{ $imageHeader ?? '' }}
            <h1 class="{{ $titleClass }}">{{ $title }}</h1>
            <div class="card-body {{ $cardBodyClass }}">
                <form x-data="{
        loading: false,
        submitForm(event) {
            const form = event.target;

            if (!form.checkValidity()) {
                form.reportValidity(); // show HTML5 validation messages
                return;
            }

            this.loading = true;
            form.submit();
        }
    }" @submit.prevent="submitForm" method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"
                    action="{{ $action }}" @if($hasFileUpload) enctype="multipart/form-data" @endif
                    class="{{ $formClass }}" novalidate>
                    @csrf
                    @if(strtoupper($method) !== 'GET' && $insertMethod !== '')
                        @if(in_array(strtoupper($insertMethod), ['PUT', 'PATCH', 'DELETE']))
                            @method($insertMethod)
                        @endif
                    @endif

                    {{ $slot }}

                    <div class="d-flex justify-content-between align-items-center flex-column flex-sm-row gap-2 mt-4">
                        @if($backRoute)
                            <x-ui.go-back-btn :fallback="$backRoute" />
                        @endif

                        <button type="submit" :disabled="loading" class="btn btn-primary px-4">
                            @if ($submitButtonIcon)
                                <i class="bi {{ $submitButtonIcon }} me-1"></i>
                            @endif

                            <span x-show="!loading">{{ $submitButtonText ?? 'Submit' }}</span>
                            <span x-show="loading" x-cloak>ველოდები...</span>
                        </button>
                    </div>
                </form>


            </div>
        </div>
        <!-- Floating Speed Dial Button -->
        @if ($hasSpeedDial)
            <x-admin.speed-dial :resourceName="$indexResourceName" />
        @endif

    </div>
</div>