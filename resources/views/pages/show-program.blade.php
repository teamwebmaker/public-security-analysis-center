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
@section('main')
   <!-- Course Header -->
   <section class="gold-bg border-bottom  border-1 border-gold-bg--light rounded-bottom-4">
      <div class="container py-5 px-3 px-sm-5">
        <div class="row g-3 g-sm-5 align-items-center">

          <!-- Image Column -->
          <div class="col-12 col-lg-6">
            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">

               <img src="{{ asset(implode('/', ['images', $category, $item->image])) }}"
                 class="w-100 h-100 object-fit-cover" alt="{{ $item->title->$language }}">
            </div>
          </div>

          <!-- Info Column -->
          <div class=" col-12 col-lg-6">
            <h1 class="fw-bold mb-4 fs-3 gold-black">{{ $item->title->$language }}</h1>

            <ul class="list-unstyled fs-6 lh-lg">
               <li class="d-flex align-items-center mb-2">
                 <img src="/images/icons/gel-currency.png" alt="gel currency icon" class="me-2 icon-currency">

                 <strong class="pe-1">Price:</strong> {{ $item->price }}
               </li>
               <li class="d-flex align-items-center mb-2">
                 <i class="bi bi-clock-fill me-2"></i>
                 <strong class="pe-1">Duration:</strong> {{ $item->duration }}
               <li class="d-flex align-items-center mb-2">
                 <i class="bi bi-calendar-event-fill me-2"></i>
                 <strong class="pe-1">Course Starts:</strong> {{ date('d.m.Y', strtotime($item->start_date)) }}
               </li>
               <li class="d-flex align-items-center">
                 <i class="bi bi-calendar-week-fill me-2"></i>
                 <strong class="pe-1">Schedule:</strong>
                 {{ collect($item->days)->map(fn($day) => mb_substr(trim($day), 0, 3))->implode(', ') }}
                 |
                 {{ $item->hour->start }} - {{ $item->hour->end }}
               </li>
            </ul>

            <div class="mt-4 d-flex flex-wrap gap-3">
               <a href="#" class="btn register-btn px-4">Register</a>
               <a href="#" class="btn border-dark px-4">For Companies</a>
            </div>
          </div>

        </div>
      </div>
   </section>

   <!-- About Section -->
   <section class="container py-5 px-3 px-sm-5">
      <h2 class="gold-text fw-bold mb-4">About the Course</h2>
      <div class="row g-4 align-items-start">
        <div class="col-md-9">
          <p class="fs-5 fw-light lh-md">
            {{ $item->description->$language }}
          </p>
        </div>
        <div class="col-7 col-md-3 col-sm-5">
          <img src="{{ asset(implode('/', ['images', $category, $item->image])) }}" class="img-fluid rounded shadow"
            alt="{{ $item->title->$language }}">
        </div>
      </div>
   </section>

   <!-- Syllabus Section -->
   <section class=" border-top border-1 border-gold-bg--light  ">
      <div class="container py-5 px-3 px-sm-5 col-12 col-sm-8 col-lg-5  ms-0 me-auto">
        <h2 class="gold-text fw-bold mb-4">Syllabus</h2>
        <div class="accordion" id="syllabusAccordion">
          @foreach ($item->syllabuses as $index => $syllabus)
           <div class="accordion-item">
            <h2 class="accordion-header" id="heading{{ $index }}">
               <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse{{ $index }}" aria-expanded="false">
                {{ $syllabus->title->$language }}
               </button>
            </h2>
            <div id="collapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#syllabusAccordion">
               <div class="accordion-body">
                <a href="{{ $syllabus->pdf }}" target="_blank" class="btn btn-sm btn-outline-secondary ">Download
                  PDF</a>
               </div>
            </div>
           </div>
         @endforeach
        </div>
      </div>

   </section>

   <!-- Instructors Section -->
   <section class="gold-bg--light rounded-top-5">
      <div class="container-lg mt-5 px-3 px-sm-5">
        <h2 class="fw-bold mb-4 text-center gold-text">Instructors</h2>

        <div class="row justify-content-center g-4 mb-5">
          @foreach($item->mentors as $mentor)
           <div class="col-12 col-md-6 col-lg-4 d-flex">
            <div class="card w-100 h-100 border-0 shadow-sm text-center p-3">
               <img src="{{ $mentor->image }}" class="rounded mx-auto mb-3"
                style="width: 100px; height: 100px; object-fit: cover;" alt="{{ $mentor['full-name'] }}">
               <div class="card-body">
                <h5 class="card-title fw-bold mb-1">{{ $mentor['full-name'] }}</h5>
                <p class="text-muted small mb-1">{{ $mentor->position ?? '' }}</p>
                <p class="card-text fs-6">{{ $mentor->description }}</p>
               </div>
            </div>
           </div>
         @endforeach
        </div>
      </div>
   </section>

   @include('partials.partners')
@endsection



@section('scripts')
   <script>
      const swiper = new Swiper(".partners", partnersSliderParams);
   </script>
@endsection