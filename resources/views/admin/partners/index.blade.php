@extends('layouts.dashboard')
@section('title', 'Project List')
@section('main')
    @session('success')
        <div class="alert alert-success" role="alert" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 3000)">
            {{ $value }}
        </div>
    @endsession
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3">
        @foreach($partners as $partner)
            <x-admin-card :title="$partner->title" :description="$partner->description" :image="'images/partners/' . $partner->image" :visibility="$partner->visibility" :edit-url="route('partners.edit', $partner)"
                :delete-url="route('partners.destroy', $partner)" />
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! $partners->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    </div>
@endsection

@section('scripts')
    <script></script>
@endsection