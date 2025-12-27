<form method="POST" id="register_to_program_form" class="needs-validation dirty-check-form p-3 text-start" novalidate
    action="{{ route('messages.store') }}">

    @csrf
    {{-- Subject --}}
    <input type="hidden" name="subject" value="{{ old('subject', 'Register to program') }}">

    {{-- Name --}}
    <div class="mb-3">
        <x-form.input name="full_name" label="{{ __('static.form.full_name') }}" value="{{ old('full_name') }}"
            placeholder="{{ __('static.form.placeholders.full_name') }}" />
    </div>

    {{-- Phone --}}
    <div class="mb-3">
        <x-form.input name="phone" label="{{ __('static.form.phone') }}" value="{{ old('phone') }}"
            placeholder="{{ __('static.form.placeholders.phone') }}" />
    </div>

    {{-- Company Name --}}
    <div class="mb-3">
        <x-form.input name="company_name" label="{{ __('static.form.company_name') }}" value="{{ old('company_name') }}"
            placeholder="{{ __('static.form.placeholders.company_name') }}" :required="false" />
    </div>

    {{-- email --}}
    <div class="mb-3">
        <x-form.input name="email" label="{{ __('static.form.email') }}" value="{{ old('email') }}"
            placeholder="{{ __('static.form.placeholders.email') }}" :required="false" />
    </div>

    {{-- Message --}}
    <div class="mb-3">
        <x-form.textarea name="message" label="{{ __('static.form.message') }}" class="form-control"
            :placeholder="__('static.form.placeholders.message')" value="{{ old('message') }}" minlength="-1"
            maxlength="250" :required="false" />
    </div>

    {{-- Consent & Send --}}
    <div class="d-flex justify-content-end align-items-center mt-4">
        <button type="submit" class="btn view-more--secondary px-4">
            <i class="bi bi-send-fill me-1"></i>
            <span>{{ __('static.form.send') }}</span>
        </button>
    </div>
</form>