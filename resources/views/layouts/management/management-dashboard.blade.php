@extends('layouts.dashboard')

@section('sidebar')
   <div class="sidebar-brand d-flex align-items-center justify-content-between justify-content-md-center p-3">

      <button class="btn-close d-md-none" aria-label="Close"></button>
   </div>
   <div class="pt-3">
      @include('partials.management.sidebar')
   </div>
@endsection

@section('topbar')
   @include('partials.management.topbar')
@endsection