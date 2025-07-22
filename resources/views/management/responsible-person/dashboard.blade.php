@extends('layouts.management.management-dashboard')
@section('title', 'ფილიალის მონაცემები')


@section('sidebar-content')
   <x-management.dashboard.sidebar-menu :items="$sidebarItems" />
@endsection
@section('main')
   <h1>THIS IS A Responsible_person DASHBOARD DATA</h1>
@endsection