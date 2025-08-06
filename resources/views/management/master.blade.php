@extends('layouts.management.management-dashboard')
@php $sidebarItems = config('sidebar.company-leader');@endphp
@section('sidebar-content')
   <x-management.dashboard.sidebar-menu :items="$sidebarItems" />
@endsection

<!-- main,title and script sections will be populated by child layouts -->