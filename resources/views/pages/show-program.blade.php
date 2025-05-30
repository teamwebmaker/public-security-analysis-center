@extends('layouts.master')
@section('title', $item->title->$language)
@section('styles')
  <style>
    body {
    font-family: inherit;
    }

    .partners {
    padding-block: 15px;
    }

    .swiper-pagination {
    --swiper-pagination-bottom: -5px;
    }
  </style>
@endsection
{{-- @section('main')
<div class="container py-5">
  <div class="row gx-3 gy-4 justify-content-center align-items-stretch gold-bg">
    <!-- Image Column -->
    <div class="col-10 col-lg-5">
      <div class="ratio ratio-16x9 custom-ratio-md">
        <img src="{{ asset(implode('/', ['images', $category, $item->image])) }}" class="img-fluid rounded w-100"
          alt="{{$item->title->$language}}">
      </div>

    </div>


    <!-- Info Column -->
    <div class="col-10 col-lg-5 d-flex my-2">
      <div class=" p-3 p-md-4 d-flex flex-column justify-content-center w-100">
        <h1 class="fw-bold mb-3 gold-black fs-3 text-start">{{$item->title->$language}}</h1>

        <div class="icon-box fs-6 mb-2 text-start">
          <img class="icon-currency me-2" src="/images/icons/gel-currency.png" alt="gel currency icon" width="20">
          <span>Price: 2450</span>
        </div>

        <div class="icon-box fs-6 mb-2 text-start">
          <i class="bi bi-clock-fill me-2"></i>
          <span>Duration: 2 Months</span>
        </div>

        <div class="icon-box fs-6 mb-2 text-start">
          <i class="bi bi-calendar-event-fill me-2"></i>
          <span>Course Starts: 17.06.2025</span>
        </div>

        <div class="icon-box fs-6 mb-2 text-start">
          <i class="bi bi-calendar-week-fill me-2"></i>
          <span>Tue, Thu, Sat | 19:30 - 22:00</span>
        </div>

        <div class="mt-4 d-flex flex-wrap gap-2">
          <a href="#" class="btn view-more fs-6">Register</a>
          <a href="#" class="btn border gold-border fs-6 px-4 text-dark">For Companies</a>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-5">
    <h2 class="fw-bold mb-4">About the Course</h2>

    <div class="d-flex flex-column flex-md-row align-items-start gap-4">
      <!-- Text Column -->
      <div class="flex-grow-1">
        <p class="fs-6 mb-0">
          With 15 years of experience training military, law enforcement, diplomats and students, CASE presents the
          intensive training course in Intelligence and National Security SSNS25. Summer school is held every year and
          counts thousands of certified professionals. CASE is the best place for career growth and professional
          networking. This is a unique opportunity for security professionals and interested parties to gain first-hand
          insider knowledge on topics that have never been more accessible, gain professional skills and gain recognized
          certification. Our trainees are law enforcement, military, lawyers, diplomats, business leaders, IT
          specialists and journalists, as well as just interested people. All this, together with the best learning
          conditions, will allow students to make valuable connections, make new friends and take their career to a new
          level.
        </p>
      </div>

      <!-- Image Column -->
      <div class="col-8 col-sm-5 col-md-2 d-flex justify-content-start justify-content-md-end">
        <img src="{{ asset(implode('/', ['images', $category, $item->image])) }}" class="img-fluid rounded"
          alt="{{$item->title->$language}}">
      </div>
    </div>
  </div>
</div>


@include('partials.partners')
@endsection --}}


@section('main')
  <div class="gold-bg">
    <div class="container py-4">
    <!-- Course Header Section -->
    <div class="row py-4 justify-content-center align-items-stretch ">
      <!-- Image Column -->
      <div class="col-12 col-lg-5">
      <div class="ratio ratio-16x9 " style="max-height: 300px; overflow: hidden;">
        <img src="{{ asset(implode('/', ['images', $category, $item->image])) }}"
        class="img-fluid rounded w-100 object-fit-cover" alt="{{ $item->title->$language }}">
      </div>
      </div>

      <!-- Info Column -->
      <div class="col-12 col-lg-5 d-flex">
      <div class="p-3 p-md-4 d-flex flex-column justify-content-center w-100">
        <h1 class="fw-bold mb-3 gold-black fs-3 text-start">{{ $item->title->$language }}</h1>

        <div class="icon-box fs-6 mb-2 text-start">
        <img class="icon-currency me-2" src="/images/icons/gel-currency.png" alt="gel currency icon" width="20">
        <span>Price: 2450</span>
        </div>

        <div class="icon-box fs-6 mb-2 text-start">
        <i class="bi bi-clock-fill me-2"></i>
        <span>Duration: 2 Months</span>
        </div>

        <div class="icon-box fs-6 mb-2 text-start">
        <i class="bi bi-calendar-event-fill me-2"></i>
        <span>Course Starts: 17.06.2025</span>
        </div>

        <div class="icon-box fs-6 mb-2 text-start">
        <i class="bi bi-calendar-week-fill me-2"></i>
        <span>Tue, Thu, Sat | 19:30 - 22:00</span>
        </div>

        <div class="mt-4 d-flex flex-wrap gap-2">
        <a href="#" class="btn view-more fs-6">Register</a>
        <a href="#" class="btn border border-dark fs-6 px-4 text-dark">For Companies</a>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>

  <!-- About the Course Section -->
  <div class="container mt-5 px-3 px-sm-5">
    <h2 class="fw-bold mb-4">About the Course</h2>
    
    <div class="row g-4 align-items-start">
      <!-- Text Column -->
      <div class="col-12 col-md-9">
        <p class="fs-6 mb-0">
          With 15 years of experience training military, law enforcement, diplomats and students, CASE presents the
          intensive training course in Intelligence and National Security SSNS25. Summer school is held every year and
          counts thousands of certified professionals. CASE is the best place for career growth and professional
          networking. This is a unique opportunity for security professionals and interested parties to gain first-hand
          insider knowledge on topics that have never been more accessible, gain professional skills and gain recognized
          certification. Our trainees are law enforcement, military, lawyers, diplomats, business leaders, IT
          specialists and journalists, as well as just interested people. All this, together with the best learning
          conditions, will allow students to make valuable connections, make new friends and take their career to a new
          level.
        </p>
      </div>
      
      <!-- Image Column -->
      <div class="col-6 col-md-3 d-flex justify-content-center justify-content-md-end">
        <img src="{{ asset(implode('/', ['images', $category, $item->image])) }}" class="img-fluid rounded shadow-sm"
        alt="{{ $item->title->$language }}">
      </div>
    </div>
  </div>
  
  <!--  Section Instructors -->
  <!-- Instructors Section -->
<div class="container-lg mt-5 px-3 px-sm-5">
  <h2 class="fw-bold mb-4">Instructors</h2>
  
  <div class="row g-4">
    @foreach($mentors as $mentor)
    <div class="col-12 col-md-6 col-lg-4">
      <div class="card h-100 border-0 shadow-sm">
        <img src="{{ $mentor->image }}" class="card-img-top rounded-top" alt="{{ $mentor->name }}">
        {{-- <img src="{{ asset('images/mentors/' . $mentor->image) }}" class="card-img-top rounded-top" alt="{{ $mentor->name }}"> --}}
        <div class="card-body">
          <h5 class="card-title fw-bold mb-2">{{ $mentor->name }}</h5>
          <p class="card-text fs-6">{{ $mentor->description }}</p>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>

 

  @include('partials.partners')
@endsection
@section('scripts')
  <script>
    const swiper = new Swiper(".partners", partnersSliderParams);
  </script>
@endsection