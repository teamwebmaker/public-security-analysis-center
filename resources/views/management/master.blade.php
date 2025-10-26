@extends('layouts.management.management-dashboard')

<!-- Global Success Display -->
@if(session()->has('success'))
   <x-ui.toast :messages="[session('success')]" type="success" />
@endif

<!-- Global Error Display -->
@if ($errors->any())
   <x-ui.toast :messages="$errors->all()" type="error" />
@endif

@section('sidebar-content')
   <x-management.dashboard.sidebar-menu :items="$sidebarItems" />
@endsection

<!-- main,title and script sections will be populated by child layouts -->