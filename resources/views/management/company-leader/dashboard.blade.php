@extends('layouts.management.management-dashboard')
@section('title', 'კომპანიის მონაცემები')
@section('sidebar-content')
   <x-management.dashboard.sidebar-menu :items="$sidebarItems" />
@endsection
@section('main')
   <p>THIS IS A COMPANY LEADER DASHBOARD DATA</p>
@endsection