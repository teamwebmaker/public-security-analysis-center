@extends('layouts.dashboard')
@section('title', 'რედაქტირება/' . $project->title->ka)

@section('main')
	<div class="row justify-content-center">
		<div class="col-lg-9">

			<!-- Global Success Message -->
			@session('success')
				<div class="alert alert-success alert-dismissible fade show mb-4" role="alert" x-data="{ show: true }"
					x-show="show" x-init="setTimeout(() => show = false, 3000)">
					<i class="bi bi-check-circle-fill me-2"></i> {{ $value }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endsession

			<div class="alert alert-warning d-none" id="validation-errors-container">
				<ul class="mb-0" id="validation-errors-list"></ul>
			</div>

			<!-- Global Error Display -->
			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible fade show mb-4">
					<ul class="mb-0">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			<div class="card border-0 shadow-sm">
				<!-- Existing Image Hero Display -->
				@if($project->image)
					<div class="card-header bg-transparent border-0 text-center position-relative p-0" style="min-height: 180px;">
						<a href="{{ asset('images/projects/' . $project->image) }}" data-fancybox data-caption="ძველი სურათი"
							class="d-block w-100 h-100">
							<img src="{{ asset('images/projects/' . $project->image) }}"
								class="img-fluid rounded-top w-100 h-100 object-fit-cover"
								style="max-height: 200px; object-position: center;">
						</a>
					</div>
				@endif

				<div class="card-body p-4">
					<form method="POST" action="{{ route('projects.update', $project) }}" enctype="multipart/form-data"
						class="needs-validation" novalidate>
						@csrf
						@method('PUT')

						<div class="mb-4">
							<h1 class="h4">პროექტის რედაქტირება</h1>
						</div>

						<!-- Tab Navigation -->
						<nav>
							<div class="nav nav-tabs" id="nav-tab" role="tablist">
								<button class="nav-link active" id="ka-tab" data-bs-toggle="tab" data-bs-target="#ka-tab-content"
									type="button" role="tab" aria-controls="ka-tab-content" aria-selected="true">
									KA
								</button>
								<button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-tab-content"
									type="button" role="tab" aria-controls="en-tab-content" aria-selected="false">
									EN
								</button>
							</div>
						</nav>

						<div class="tab-content pt-3" id="nav-tabContent">
							<!-- title and description ka -->
							<div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab">
								<div class="mb-3">
									<input type="text" class="form-control @error('title_ka') is-invalid @enderror" name="title_ka"
										placeholder="სათაური" value="{{ old('title_ka', $project->title->ka) }}" required>
									@error('title_ka')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								<div class="mb-3">
									<textarea class="form-control @error('description_ka') is-invalid @enderror" rows="5"
										name="description_ka" placeholder="აღწერა" minlength="10"
										required>{{ trim(old('description_ka', $project->description->ka)) }}</textarea>
									@error('description_ka')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
							<!-- title and description en -->
							<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
								<div class="mb-3">
									<input type="text" class="form-control @error('title_en') is-invalid @enderror" name="title_en"
										placeholder="Title" value="{{ old('title_en', $project->title->en) }}" required>
									@error('title_en')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
								<div class="mb-3">
									<textarea class="form-control @error('description_en') is-invalid @enderror" rows="5"
										name="description_en" placeholder="Description"
										required>{{ trim(old('description_en', $project->description->en)) }}</textarea>
									@error('description_en')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>
						<div class="row mb-4">
							<!-- Image -->
							<div class="col-md-6">
								<label for="image" class="form-label">ახალი სურათი</label>

								<input type="file" id="project-image" name="image"
									class="form-control @error('image') is-invalid @enderror"
									accept="image/jpeg,image/png,image/webp">
								<small class="form-text text-muted">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა:
									5MB</small>
								<div class="mt-2 text-center">
									<img id="project-image-preview" src="#" alt="Image preview" class="img-thumbnail d-none"
										style="max-height: 150px;" data-fancybox>
								</div>
								@error('image')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<!-- Visibility -->
							<div class="col-md-6">
								<label for="visibility" class="form-label">ხილვადობა</label>
								<select id="visibility" name="visibility"
									class="form-select @error('visibility') is-invalid @enderror">
									<option value="1" {{ old('visibility', $project->visibility) == '1' ? 'selected' : '' }}>
										ხილული
									</option>
									<option value="0" {{ old('visibility', $project->visibility) == '0' ? 'selected' : '' }}>
										დამალული
									</option>
								</select>
								@error('visibility')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
							<x-go-back-button fallback="partners.index" />
							<button type="submit" class="btn btn-primary px-4">
								<i class="bi bi-check-lg me-2"></i> განახლება
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script type="module" src="{{ asset('scripts/project/projectEdit.js') . '?date=' . $modified }}"></script>
@endsection