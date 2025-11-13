@extends('layouts.admin.admin-dashboard')

@section('title', 'კომპანიის შექმნა')

@section('main')
    <x-admin.crud.form-container
        method="POST"
        title="კომპანიის შექმნა"
        action="{{ route($resourceName . '.store') }}"
        :backRoute="$resourceName . '.index'">

        <!-- Name and Identification Code -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.input
                    name="name"
                    label="სახელი"
                    value="{{ old('name') }}"
                    placeholder="შეიყვანეთ სახელი"
                />
            </div>

            <div class="col-md-6 mb-3">
                <x-form.input
                    name="identification_code"
                    label="საიდენთიფიკაციო კოდი"
                    value="{{ old('identification_code') }}"
                    placeholder="შეიყვანეთ საიდენთიფიკაციო კოდი"
                />
            </div>
        </div>

        <!-- Economic Activity Type & Code -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.select
                    name="economic_activity_type_id"
                    :options="$economic_activity_types"
                    value="{{ old('economic_activity_type_id') }}"
                    label="ეკონომიკური საქ ტიპი"
                    :required="false"
                />
            </div>

            <div class="col-md-6 mb-3">
                <x-form.input
                    name="economic_activity_code"
                    label="ეკონომიკური საქმიანობის კოდი"
                    value="{{ old('economic_activity_code') }}"
                    placeholder="შეიყვანეთ ეკონომიკური საქმიანობის კოდი"
                />
            </div>
        </div>

        <!-- Risk Level & Visibility -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-form.select
                    name="risk_level"
                    :options="[
                        'extremely high' => 'განსაკუთრებით მაღალი',
                        'very high'      => 'ძალიან მაღალი',
                        'high'           => 'მაღალი',
                        'medium'         => 'საშუალო',
                        'low'            => 'დაბალი',
                    ]"
                    selected="low"
                    label="რისკის დონე"
                />
            </div>

            <div class="col-md-6 mb-3">
                <x-form.select
                    name="visibility"
                    :options="['1' => 'ხილული', '0' => 'დამალული']"
                    selected="1"
                    label="ხილვადობა"
                />
            </div>
        </div>

        <!-- High Risk Activities & Evacuation Plan -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">მაღალი რისკის შემცველობა</label>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="high_risk_activities"
                        id="high_risk_yes"
                        value="1"
                        {{ old('high_risk_activities') == 1 ? 'checked' : '' }}
                    >
                    <label for="high_risk_yes" class="form-check-label">კი</label>
                </div>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="high_risk_activities"
                        id="high_risk_no"
                        value="0"
                        {{ old('high_risk_activities') == 0 ? 'checked' : '' }}
                    >
                    <label for="high_risk_no" class="form-check-label">არა</label>
                </div>

                @error('high_risk_activities')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">საევაკუაციო გეგმა</label>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="evacuation_plan"
                        id="evacuation_plan_yes"
                        value="1"
                        {{ old('evacuation_plan') == 1 ? 'checked' : '' }}
                    >
                    <label for="evacuation_plan_yes" class="form-check-label">კი</label>
                </div>

                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="radio"
                        name="evacuation_plan"
                        id="evacuation_plan_no"
                        value="0"
                        {{ old('evacuation_plan') == 0 ? 'checked' : '' }}
                    >
                    <label for="evacuation_plan_no" class="form-check-label">არა</label>
                </div>

                @error('evacuation_plan')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Admins -->
        <div class="col-md-5">
            <x-form.checkbox-dropdown
                label="ადმინები"
                :items="$users"
                name="user_ids"
                labelField="full_name"
                :selected="old('user_ids')"
            />
        </div>

    </x-admin.crud.form-container>
@endsection
