@extends('layouts.master')
@section('title',  'Contact Page')

@section('styles')
    <style>
        .partners {
            padding-block: 15px;
        }
        .swiper-pagination {
            --swiper-pagination-bottom: -5px;
        }

        ::placeholder {
            color: var(--gold) !important;
        }

        .form-control{
            border-color: var(--gold);
            color: var(--gold) !important;
        }

        .form-control:focus{
            box-shadow: 0 0 0 5px hsla(var(--gold-hue), var(--gold-saturation), var(--gold-lightness), 0.5);
            border-color: var(--gold);
            outline: none;
        }
    </style>
@endsection

@section('main')
    <main>
        <div class="container-fluid pt-5">
            <div class="container">
                @session('success')
                    <div class="alert alert-success" role="alert"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                        {{ $value }}
                    </div>
                @endsession
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d190570.98345666463!2d44.64195469052535!3d41.72760440547997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40440cd7e64f626b%3A0x61d084ede2576ea3!2sTbilisi!5e0!3m2!1sen!2sge!4v1705256468146!5m2!1sen!2sge" title="Location" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <form method="POST" action="{{ route('send.notification') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <input type="text" class="form-control " name="full_name" placeholder="Full Name"  />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" name="subject" placeholder="Subject"  />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <input type="email" class="form-control" name="email" placeholder="Enter Email"  />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <input type="number" class="form-control" name="phone" placeholder="Phone"  />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control"  rows="5" name="message" placeholder="Message"></textarea>
                            </div>
                            <button type="submit" class="btn black-bg gold-text">Send Message</button>
                        </form>
                    </div>
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
@endsection
