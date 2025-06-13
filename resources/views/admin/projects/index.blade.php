@extends('layouts.dashboard')
@section('title', 'Project List')
@section('main')
    @session('success')
        <div class="alert alert-success" role="alert" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 3000)">
            {{ $value }}
        </div>
    @endsession
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($projects as $project)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <img class="response-img" src="{{ asset('images/projects/' . $project->image) }}" />
                    </div>
                    <div class="card-body">
                        <h3 class="card-title truncate" title="{{ $project->title->ka }}">
                            {{ $project->title->ka }}
                        </h3>
                        <p class="line-clamp">
                            {{ $project->description->ka }}
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a type="button" class="btn btn-success d-flex gap-2"
                            href="{{ route('projects.edit', ['project' => $project]) }}">
                            <i class="bi bi-pencil-square"></i>
                            <span class="text-label">Edit</span>
                        </a>
                        <form method="POST" action="{{ route('projects.destroy', ['project' => $project]) }}"
                            onsubmit="return confirm('წავშალოთ პროექტი?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger d-flex gap-2">
                                <i class="bi bi-trash"></i>
                                <span class="text-label">Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! $projects->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    </div>
@endsection

@section('scripts')
    <script></script>
@endsection