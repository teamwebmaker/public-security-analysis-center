@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $document_template->name)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT"
		action="{{ route($resourceName . '.update', $document_template) }}" title="ინსტრუქტაჟის რედაქტირება"
		:hasFileUpload="true" :backRoute="$resourceName . '.index'">

		<div class="row">
			<!--  document_template name -->
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" value="{{ old('name', $document_template->name) }}"
					placeholder="შეიყვანეთ სახელი" />
			</div>
			<!-- Document -->
			<div class="col-md-6 mb-3">
				<x-form.input type="file" name="document" label="დოკუმენტი" :isImage="false"
					accept=".pdf, .doc, .docx, .xls, .xlsx" infoMessage="მხარდაჭერილი ფორმატები: .pdf, .doc, .docx, .xls, .xlsx"
					:required="false" />
			</div>
		</div>


		<div class="row">
			<!-- Workers -->
			<div class="col-md-6 mb-3">
				<x-form.checkbox-dropdown label="შემსრულებლები" :items="$workers" name="worker_ids" labelField="full_name"
					:selected="old('worker_ids', $document_template->users->pluck('id')->toArray())" />
				<div class="form-text">
					მონიშნეთ ის შემსრულებლები რომლებმაც უნდა ნახონ ინსტრუქტაჟო
				</div>
			</div>

			<!-- Visibility -->
			<div class="col-md-6 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ old('visibility', $document_template->visibility) }}" label="ხილვადობა" />
			</div>
		</div>
	</x-admin.crud.form-container>
@endsection