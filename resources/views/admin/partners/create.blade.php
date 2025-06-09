@extends('layouts.dashboard')
@section('title', 'პარტნიორის შექმნა')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <form method="POST" action="{{ route('partners.store') }}" enctype="multipart/form-data">
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

                <div class="mb-3">
                    <input type="text" class="form-control @error('title') is-invalid @enderror" name="title"
                        value="{{ old('title') }}" placeholder="სათაური" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <input type="url" class="form-control @error('link') is-invalid @enderror" name="link"
                        value="{{ old('link') }}" placeholder="ლინკი" required>
                    @error('link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
    <script></script>
@endsection