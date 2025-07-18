@extends('layouts.master-dashboard')

@section('sidebar')
   <div class="sidebar-brand d-flex align-items-center justify-content-between justify-content-md-center p-3">
      <span class="fs-5 fw-bold" style="cursor: default;">ადმინისტრატორი</span>
      <button class="btn-close d-md-none" aria-label="Close"></button>
   </div>
   <div class="pt-3">
      <x-admin.dashboard.sidebar />
   </div>
@endsection

@section('topbar')
   <x-admin.dashboard.topbar />
@endsection