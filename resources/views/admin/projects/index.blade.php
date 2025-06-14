@extends('layouts.dashboard')
@section('title', 'Project List')
@section('main')
    @session('success')
        <div class="alert alert-success" role="alert" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 3000)">
            {{ $value }}
        </div>
    @endsession
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 ">
        @foreach($projects as $project)
            <x-admin-card :title="$project->title->ka" :description="$project->description->ka" :image="'images/projects/' . $project->image" :edit-url="route('projects.edit', $project)"
                :delete-url="route('projects.destroy', $project)" />
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