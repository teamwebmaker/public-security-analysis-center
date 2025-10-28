@extends('layouts.admin.admin-dashboard')
@section('title', 'შაბლონის შექმნა')
@section('main')

	<x-admin.crud.form-container title="შაბლონის შექმნა" action="{{ route($resourceName . '.store') }}" method="POST"
		:hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<!-- Instruction name & video link -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" placeholder="შეიყვანეთ სახელი" value="{{ old('name') }}" />
			</div>
			<!-- Document -->
			<div class="col-md-6 mb-3">
				<x-form.input type="file" name="document" label="დოკუმენტი" :isImage="false"
					accept=".pdf, .doc, .docx, .xls, .xlsx"
					infoMessage="მხარდაჭერილი ფორმატები: .pdf, .doc, .docx, .xls, .xlsx" />
			</div>
		</div>

		<div class="row">

			<!-- Workers -->
			<div class="col-md-6 mb-3">
				<x-form.checkbox-dropdown label="შემსრულებლები" :items="$workers" name="worker_ids" labelField="full_name"
					:selected="old('worker_ids')" />
				<div class="form-text">
					მონიშნეთ ის შემსრულებლები რომლებმაც უნდა ნახონ ინსტრუქტაჟო
				</div>
			</div>

			<div class="col-md-6 mb-3">
				<!-- Visibility -->

				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
					label="ხილვადობა" />

			</div>
		</div>
	</x-admin.crud.form-container>
@endsection