@extends('layouts.dashboard')
@section('title', 'პროექტის შექმნა')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Global Error Display --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="ka-tab" data-bs-toggle="tab" data-bs-target="#ka-tab-content"
                            type="button" role="tab" aria-controls="ka-tab-content" aria-selected="true">KA</button>
                        <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-tab-content"
                            type="button" role="tab" aria-controls="en-tab-content" aria-selected="false">EN</button>
                    </div>
                </nav>

                <div class="tab-content pt-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab"
                        tabindex="0">
                        <div class="mb-3">
                            <input type="text" class="form-control @error('title_ka') is-invalid @enderror" name="title_ka"
                                placeholder="სათაური" value="{{ old('title_ka') }}" required>
                            @error('title_ka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control @error('description_ka') is-invalid @enderror" rows="5"
                                name="description_ka" placeholder="აღწერა" required>{{ old('description_ka') }}</textarea>
                            @error('description_ka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab" tabindex="0">
                        <div class="mb-3">
                            <input type="text" class="form-control @error('title_en') is-invalid @enderror" name="title_en"
                                placeholder="Title" value="{{ old('title_en') }}" required>
                            @error('title_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control @error('description_en') is-invalid @enderror" rows="5"
                                name="description_en" placeholder="description"
                                required>{{ old('description_en') }}</textarea>
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" required>
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">დამატება</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Preserve active tab after validation error
        document.addEventListener("DOMContentLoaded", function () {
            const activeTabId = sessionStorage.getItem('activeTab');
            if (activeTabId) {
                const trigger = document.querySelector(`[id="${activeTabId}"]`);
                if (trigger) {
                    new bootstrap.Tab(trigger).show();
                }
            }

            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    sessionStorage.setItem('activeTab', event.target.id);
                });
            });
        });
    </script>
@endsection