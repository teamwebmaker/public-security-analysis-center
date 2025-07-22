@extends('layouts.management.management-dashboard')
@section('title', 'მონაცემთა პანელი')


@section('sidebar-content')
   <x-management.dashboard.sidebar-menu :items="$sidebarItems" />
@endsection
@section('main')
   <h1>THIS IS A WORKER DASHBOARD DATA</h1>
@endsection