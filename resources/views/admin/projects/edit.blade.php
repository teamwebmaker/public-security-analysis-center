@extends('layouts.dashboard')
@section('title', 'რედაქტირება/' . $project->title->ka)

@section('main')

    {{-- Global Success Display --}}
    @session('success')
        <div class="alert alert-success" role="alert" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 3000)">
            {{ $value }}
        </div>
    @endsession

    {{-- Global warning Display --}}
    <div class="alert alert-warning d-none" id="validation-errors-container">
        <ul class="mb-0" id="validation-errors-list"></ul>
    </div>

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

    <div class="row">
        <div class="col-md-12">
            <form method="POST" enctype="multipart/form-data"
                action="{{ route('projects.update', ['project' => $project]) }}">
                @csrf
                @method('PUT')

                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="ka-tab" data-bs-toggle="tab" data-bs-target="#ka-tab-content"
                            type="button" role="tab" aria-controls="ka-tab-content" aria-selected="true"><i
                                class="bi bi-translate me-2"></i>ქართული</button>
                        <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-tab-content"
                            type="button" role="tab" aria-controls="en-tab-content" aria-selected="false"> <i
                                class="bi bi-translate me-2"></i>English</button>
                    </div>
                </nav>

                <div class="tab-content pt-2" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab"
                        tabindex="0">
                        <div class="mb-3">
                            <input type="text" class="form-control @error('title_ka') is-invalid @enderror" name="title_ka"
                                placeholder="სათაური" value="{{ old('title_ka', $project->title->ka) }}" required />
                            @error('title_ka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control @error('description_ka') is-invalid @enderror" rows="5"
                                name="description_ka" placeholder="აღწერა" minlength="10"
                                required>{{ old('description_ka', $project->description->ka) }}</textarea>
                            @error('description_ka')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="tab-pane fade pt-2" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab"
                        tabindex="0">
                        <div class="mb-3">
                            <input type="text" class="form-control @error('title_en') is-invalid @enderror" name="title_en"
                                placeholder="Title" value="{{ old('title_en', $project->title->en) }}" required />
                            @error('title_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control @error('description_en') is-invalid @enderror" rows="5"
                                name="description_en" placeholder="Description"
                                required>{{ old('description_en', $project->description->en) }}</textarea>
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" />
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row justify-content-between">
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary">განახლება</button>
                    </div>
                    <div class="col-6">
                        <a href="{{ asset('images/projects/' . $project->image) }}" data-fancybox
                            data-caption="{{ $project->title->ka }}">
                            <img class="w-25 float-end rounded" src="{{ asset('images/projects/' . $project->image) }}" />
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        Fancybox.bind('[data-fancybox]', {});
    </script>
@endsection