@extends('management.master')

@section('title', 'სამუშაოს შექმნა')

@section('main')
    @if (empty($branches))
        <div class="alert alert-warning">
            <i class="bi bi-building-exclamation me-1"></i>
            სამუშაოს შესაქმნელად ადმინისტრატორმა ჯერ უნდა დაგაკავშიროთ შესაბამის კომპანიასთან.
        </div>
    @endif

    <x-admin.crud.form-container method="POST" title="სამუშაოს შექმნა"
        action="{{ route('management.worker.tasks.store') }}"
        backRoute="management.dashboard.tasks" :hasSpeedDial="false"
        submitButtonText="სამუშაოს შექმნა">

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.select name="service_mode"
                    :options="['existing' => 'არსებული სერვისი', 'temporary' => 'დროებითი სერვისი']"
                    :selected="old('service_mode', old('temporary_service_name') ? 'temporary' : 'existing')"
                    label="სერვისის ტიპი" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3" data-existing-service-field>
                <x-form.select name="service_id" :options="$services" label="სერვისი"
                    :selected="old('service_id')" :required="false" />
            </div>

            <div class="col-md-6 mb-3" data-temporary-service-field>
                <x-form.input name="temporary_service_name" label="დროებითი სერვისის სახელი"
                    placeholder="შეიყვანეთ მხოლოდ ამ სამუშაოსთვის" :required="false"
                    value="{{ old('temporary_service_name') }}" maxlength="255" />
                <div class="form-text">სახელი შეინახება მხოლოდ ამ სამუშაოსა და მის ციკლებში.</div>
            </div>

            <div class="col-md-6 mb-3">
                <x-form.select name="branch_id" :options="$branches" :selected="old('branch_id')"
                    label="სამიზნე ფილიალი" />
            </div>
        </div>

        <div class="row" data-recurrence-group>
            <div class="col-md-6 mb-3" data-recurrence-toggle>
                <x-form.select name="is_recurring" :options="['1' => 'დიახ', '0' => 'არა']"
                    :selected="old('is_recurring', '0')" label="განმეორებადი სამუშაო" />
            </div>

            <div class="col-md-6 mb-3" data-recurrence-interval>
                <x-form.input type="number" name="recurrence_interval"
                    label="განმეორების ინტერვალი (დღე)" placeholder="მაგ: 45"
                    min="1" step="1" :required="false" value="{{ old('recurrence_interval') }}" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.select name="requires_document" :options="['1' => 'დიახ', '0' => 'არა']"
                    :selected="old('requires_document', '1')" label="სჭირდება დოკუმენტი" />
            </div>
        </div>

        <div class="alert alert-info mb-0">
            <i class="bi bi-person-check me-1"></i>
            სამუშაოს შექმნის შემდეგ თქვენ ავტომატურად მიებმევით მას.
        </div>
    </x-admin.crud.form-container>
@endsection

@section('scripts')
    {!! load_script('scripts/task/taskCreate.js') !!}
@endsection
