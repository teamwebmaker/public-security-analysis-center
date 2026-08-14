@extends('layouts.admin.admin-dashboard')
@section('title', 'რედაქტირება: ' . $instruction->name)

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT"
		action="{{ route($resourceName . '.update', $instruction) }}" title="ინსტრუქტაჟის რედაქტირება" :hasFileUpload="true"
		:backRoute="$resourceName . '.index'">


		<!-- Instruction name & video link -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.input name="name" label="სახელი" value="{{ old('name', $instruction->name) }}"
					placeholder="შეიყვანეთ სახელი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input type="url" name="link" label="ვიდეო ლინკი" placeholder="https://example.com" icon="link-45deg"
					value="{{ old('link', $instruction->link) }}" :required="false" />
			</div>
		</div>


		<div class="row">
			<!-- Document -->
			<div class="col-md-6 mb-3">
				<x-form.input type="file" name="document" label="დოკუმენტი" :isImage="false"
					accept=".pdf, .doc, .docx, .xls, .xlsx" infoMessage="მხარდაჭერილი ფორმატები: .pdf, .doc, .docx, .xls, .xlsx"
					:required="false" />
			</div>

			<!-- Workers -->
			<div class="col-md-6 mb-3">
				<x-form.checkbox-dropdown label="შემსრულებლები" :items="$workers" name="worker_ids" labelField="full_name"
					:selected="old('worker_ids', $instruction->users->pluck('id')->toArray())" />
				<div class="form-text">
					მონიშნეთ ის შემსრულებლები რომლებმაც უნდა ნახონ ინსტრუქტაჟო
				</div>
			</div>
		</div>

		<!-- Visibility -->
		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					:selected="old('visibility', $instruction->visibility)" label="ხილვადობა" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="document_visibility" :options="['private' => 'პირადი', 'public' => 'საჯარო']"
					:selected="old('document_visibility', $instruction->document_visibility)" label="დოკუმენტის გაზიარება" />
				<div class="form-text">პირადზე გადართვისას არსებული საჯარო ბმული გაუქმდება.</div>
			</div>
		</div>

		@if ($instruction->isPublic() && $instruction->publicShare?->isUsable())
			<div class="alert alert-info">
				<strong>საჯარო ბმული:</strong>
				<a href="{{ route('public-shares.show', $instruction->publicShare->token) }}" target="_blank" rel="noopener">
					{{ route('public-shares.show', $instruction->publicShare->token) }}
				</a>
			</div>
		@endif
	</x-admin.crud.form-container>
@endsection
