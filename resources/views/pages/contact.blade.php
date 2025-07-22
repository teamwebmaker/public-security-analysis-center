@extends('layouts.master')
@section('title', 'Contact Page')

@section('styles')
    <style>
        ::placeholder {
            color: var(--gold) !important;
        }

        .form-control {
            border-color: var(--gold);
            color: var(--gold) !important;
        }

        .form-control:focus {
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
                @if(session()->has('success'))
                    <x-ui.toast :messages="[session('success')]" type="success" />
                @endif
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="ratio ratio-16x9">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d190570.98345666463!2d44.64195469052535!3d41.72760440547997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40440cd7e64f626b%3A0x61d084ede2576ea3!2sTbilisi!5e0!3m2!1sen!2sge!4v1705256468146!5m2!1sen!2sge"
                                title="Location" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <form method="POST" class="needs-validation dirty-check-form" novalidate
                            action="{{ route('contacts.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <x-form.input name="full_name" class="form-control" placeholder="Full Name"
                                            value="{{ old('full_name') }}" />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <x-form.input name="subject" class="form-control" placeholder="Subject"
                                            value="{{ old('subject') }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <x-form.input type="email" name="email" class="form-control"
                                            placeholder="Enter Email" value="{{ old('email') }} " autocomplete="email" />
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="mb-3">
                                        <x-form.input type="tel" name="phone" class="form-control" placeholder="Phone"
                                            value="{{ old('phone') }}" :required="false" minlength="5" autocomplete="tel" />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <x-form.textarea name="message" class="form-control" placeholder="Message"
                                    value="{{ old('message') }}" minlength="5" maxlength="500" />
                            </div>
                            <button type="submit" class="btn black-bg gold-text">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    {!! load_script('scripts/bootstrap/bootstrapValidation.js') !!}
@endsection