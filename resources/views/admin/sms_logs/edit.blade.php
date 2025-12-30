@extends('layouts.admin.admin-dashboard')
@section('title', 'SMS ლოგის რედაქტირება')
@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" title="SMS ლოგის რედაქტირება"
		action="{{ route($resourceName . '.update', $sms_log) }}" :backRoute="$resourceName . '.index'">

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="destination" label="ადრესატი" value="{{ old('destination', $sms_log->destination) }}"
					placeholder="მიმღების ნომერი" />
			</div>
			<div class="col-md-3 mb-3">
				<x-form.select name="smsno" label="ტიპი" :options="[1 => 'რეკლამა', 2 => 'ინფორმაცია']"
					:selected="old('smsno', $sms_log->smsno)" />
			</div>
		</div>

		<div class="row">
			<div class="col-12 mb-3">
				<x-form.textarea name="content" label="მესიჯი" value="{{ old('content', $sms_log->content)}}" minlength="5"
					maxlength="255" rows="5" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection
