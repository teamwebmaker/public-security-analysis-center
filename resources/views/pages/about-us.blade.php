{{-- @extends('layouts.master')
@section('title', 'About us Page')

@section('styles')
<style>
    .partners {
        padding-block: 10px;
    }

    .swiper-pagination {
        --swiper-pagination-bottom: -5px;
    }
</style>
@endsection

@section('main')
<main>
    <div class="container-fluid">
        <div class="container-sm">
            <div class="row">
                <div class="col-12">
                    <h2 class="gold-text fw-bold mb-4">About Us</h2>
                </div>
            </div>
        </div>
        @include('partials.partners')
</main>
@endsection

@section('scripts')
<script>
    const swiper = new Swiper(".partners", partnersSliderParams);
</script>
@endsection --}}


@extends('layouts.master')
@section('title', 'About us Page')

@section('styles')
    <style>
        .about-section {
            padding: 5rem 0;
        }

        .about-content {
            position: relative;
        }

        .about-image {
            border-radius: 12px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
            object-fit: cover;
        }

        .about-image:hover {
            transform: translateY(-5px);
        }

        .about-title {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }


        .about-description {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .about-highlights {
            margin-top: 2rem;
        }

        .highlight-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .highlight-icon {
            color: var(--gold);
            font-size: 1.5rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .partners {
            padding-block: 10px;
        }

        .swiper-pagination {
            --swiper-pagination-bottom: -5px;
        }

        @media (max-width: 768px) {
            .about-section {
                padding: 3rem 0;
            }

            .about-title {
                font-size: 2rem;
            }

            .about-image {
                margin-bottom: 2rem;
            }
        }
    </style>
@endsection

@section('main')
    <main>
        <section class="about-section">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Image Column -->
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <img src="{{ asset($item->image) }}" alt="{{ $item->title->$language }}"
                            class="about-image img-fluid w-100">
                    </div>

                    <!-- Content Column -->
                    <div class="col-lg-6">
                        <h2 class="about-title gold-text fw-bold">{{ $item->title->$language }}</h2>

                        <div class="about-description">
                            {!! nl2br(e($item->description->$language)) !!}
                        </div>

                        <div class="about-highlights">
                            <div class="highlight-item">
                                <div class="highlight-icon">
                                    <i class="bi bi-award-fill"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">15+ Years Experience</h5>
                                    <p class="mb-0">Providing top-quality training since 2008</p>
                                </div>
                            </div>

                            <div class="highlight-item">
                                <div class="highlight-icon">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">5000+ Graduates</h5>
                                    <p class="mb-0">Professionals trained across multiple industries</p>
                                </div>
                            </div>

                            <div class="highlight-item">
                                <div class="highlight-icon">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Industry Leaders</h5>
                                    <p class="mb-0">Partnered with top organizations worldwide</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @include('partials.partners')
    </main>
@endsection

@section('scripts')
    <script>
        const swiper = new Swiper(".partners", partnersSliderParams);
    </script>
@endsection