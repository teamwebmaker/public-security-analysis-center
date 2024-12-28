@extends('layouts.dashboard')
@section('title', 'პარტნიორის შექმნა')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <form method="POST"  action="{{ route('partners.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control"  name="title" required placeholder="სათაური"/>
                </div>
                <div class="mb-3">
                    <input type="url" class="form-control"  name="link" required placeholder="ლინკი"/>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control"  name="image" required/>
                </div>
                <button type="submit" class="btn btn-primary">დამატება</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script></script>
@endsection

