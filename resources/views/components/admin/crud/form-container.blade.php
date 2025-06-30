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
                <form method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" @if($hasFileUpload) enctype="multipart/form-data" @endif class="{{ $formClass }}" novalidate>
                    @csrf
                    <!-- apply method('PUT') or method('PATCH') or method('DELETE') conditionally -->
                    @if(strtoupper($method) !== 'GET' && $insertMethod !== '')
                    @if(in_array(strtoupper($insertMethod), ['PUT', 'PATCH', 'DELETE']))
                    @method($insertMethod)
                    @endif
                    @endif

                    {{ $slot }}

                    <!-- Action buttons -->
                    <div class="d-flex justify-content-between align-items-center flex-column flex-sm-row gap-2 mt-4">
                        @if($backRoute)
                        <x-go-back-button :fallback="$backRoute" />
                        @endif
                        <button type="submit" class="btn btn-primary px-4">
                            @if($submitButtonIcon)
                            <i class="{{ $submitButtonIcon }} me-2"></i>
                            @endif
                            {{ $submitButtonText }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Floating Speed Dial Button -->
        <x-admin.speed-dial :resourceName="$indexResourceName" />
    </div>
</div>