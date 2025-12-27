@extends('layouts.admin.admin-dashboard')

@section('title', 'ციკლის რედაქტირება')

@section('main')
	<x-admin.crud.form-container method="POST" insertMethod="PUT" :title="'ციკლის რედაქტირება #' . $taskOccurrence->id . ' (სამუშაო #' . $taskOccurrence->task_id . ')'" :action="route('task-occurrences.update', $taskOccurrence)"
		:backRoute="'tasks.index'" :hasFileUpload="true">

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="status_id" :options="$statuses" selected="{{ $taskOccurrence->status_id }}"
					label="სტატუსი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']"
					selected="{{ $taskOccurrence->visibility }}" label="ხილვადობა" />
			</div>
		</div>

		<div class="row">
			<div class="col-md-6 mb-3">
				<x-form.select name="requires_document" :options="['1' => 'დიახ', '0' => 'არა']"
					selected="{{ $taskOccurrence->requires_document ? '1' : '0' }}" label="სჭირდება დოკუმენტი" />
			</div>
			<div class="col-md-6 mb-3">
				<x-form.input type="file" name="document" label="დოკუმენტი" :isImage="false"
					accept=".pdf,.doc,.docx,.xls,.xlsx" :required="false" />
				@if($taskOccurrence->document_path)
					<div class="form-check mt-2">
						<input class="form-check-input" type="checkbox" value="1" id="delete_document" name="delete_document">
						<label class="form-check-label" for="delete_document">მიმაგრებული დოკუმენტის წაშლა</label>
					</div>
				@endif
				@if($taskOccurrence->document_path)
					<p class="small mt-2">
						ამჟამინდელი: <a href="{{ asset('documents/tasks/' . ltrim($taskOccurrence->document_path, '/')) }}"
							target="_blank">ნახვა</a>
					</p>
				@endif
			</div>
		</div>

	</x-admin.crud.form-container>
@endsection