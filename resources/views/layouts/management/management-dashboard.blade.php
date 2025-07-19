@extends('layouts.master-dashboard')

@section('sidebar')
   <x-management.dashboard.sidebar-header />
   <div class="pt-3">
      @yield('sidebar-content')
   </div>
@endsection

@section('topbar')
   @include('partials.management.topbar')
@endsection