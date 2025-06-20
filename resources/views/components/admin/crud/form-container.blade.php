<div class="row justify-content-center">
    <div class="col-lg-9">
        <!-- Global Success Display -->
        @session('success')
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" x-data="{ show: true }"
                x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <i class="bi bi-check-circle-fill me-2"></i> {{ $value }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endsession
        <div class="card {{ $cardClass }}">
            {{ $imageHeader ?? '' }}
            <div class="card-body {{ $cardBodyClass }}">
                <h1 class="{{ $titleClass }}">{{ $title }}</h1>
                <form method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}"
                    @if($hasFileUpload) enctype="multipart/form-data" @endif class="{{ $formClass }}"
                     novalidate 
                     >
                    @csrf
                    @if(strtoupper($method) !== 'GET' && $insertMethod !== '')
                        @if(in_array(strtoupper($insertMethod), ['PUT', 'PATCH', 'DELETE']))
                            @method($insertMethod)
                        @endif
                    @endif

                    <!-- Global Error Display -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
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
    </div>
</div>