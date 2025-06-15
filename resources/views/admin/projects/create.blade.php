{{-- @extends('layouts.dashboard')
@section('title', 'პროექტის შექმნა')
@section('main')
<div class="row">
	<div class="col-md-12">
		<form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data">
			@csrf

			@if ($errors->any())
			<div class="alert alert-danger">
				<ul class="mb-0">
					@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
			@endif

			<nav>
				<div class="nav nav-tabs" id="nav-tab" role="tablist">
					<button class="nav-link active" id="ka-tab" data-bs-toggle="tab" data-bs-target="#ka-tab-content"
						type="button" role="tab" aria-controls="ka-tab-content" aria-selected="true"> <i
							class="bi bi-translate me-2"></i>ქართული</button>
					<button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-tab-content" type="button"
						role="tab" aria-controls="en-tab-content" aria-selected="false"> <i
							class="bi bi-translate me-2"></i>English</button>
				</div>


			</nav>

			<div class="tab-content pt-2" id="nav-tabContent">
				<div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel" aria-labelledby="ka-tab"
					tabindex="0">
					<div class="mb-3">
						<input type="text" class="form-control @error('title_ka') is-invalid @enderror" name="title_ka"
							placeholder="სათაური" value="{{ old('title_ka') }}" required>
						@error('title_ka')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					<div class="mb-3">
						<textarea class="form-control @error('description_ka') is-invalid @enderror" rows="5"
							name="description_ka" placeholder="აღწერა" minlength="10"
							required>{{ old('description_ka') }}</textarea>
						@error('description_ka')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>

				<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab" tabindex="0">
					<div class="mb-3">
						<input type="text" class="form-control @error('title_en') is-invalid @enderror" name="title_en"
							placeholder="Title" value="{{ old('title_en') }}" required>
						@error('title_en')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
					<div class="mb-3">
						<textarea class="form-control @error('description_en') is-invalid @enderror" rows="5"
							name="description_en" placeholder="description" required>{{ old('description_en') }}</textarea>
						@error('description_en')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>
			</div>

			<div class="mb-3">
				<input type="file" class="form-control @error('image') is-invalid @enderror" name="image" required>
				@error('image')
				<div class="invalid-feedback">{{ $message }}</div>
				@enderror
			</div>

			<button type="submit" class="btn btn-primary">დამატება</button>
		</form>
	</div>
</div>
@endsection

@section('scripts')
<script>
	// Preserve active tab after validation error
	document.addEventListener("DOMContentLoaded", function () {
		const activeTabId = sessionStorage.getItem('activeTab');
		if (activeTabId) {
			const trigger = document.querySelector(`[id="${activeTabId}"]`);
			if (trigger) {
				new bootstrap.Tab(trigger).show();
			}
		}

		document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
			tab.addEventListener('shown.bs.tab', function (event) {
				sessionStorage.setItem('activeTab', event.target.id);
			});
		});
	});
</script>
@endsection --}}


@extends('layouts.dashboard')
@section('title', 'პროექტის შექმნა')
@section('main')
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card border-0 shadow-sm">
				<div class="card-body p-4">
					<h1 class="h4 mb-4">პროექტის შექმნა</h1>

					<form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data"
						class="needs-validation" novalidate>
						@csrf

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

						<div class="mb-4">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs nav-fill" id="languageTabs" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link active d-flex align-items-center justify-content-center" id="ka-tab"
										data-bs-toggle="tab" data-bs-target="#ka-tab-content" type="button" role="tab"
										aria-controls="ka-tab-content" aria-selected="true">
										KA
									</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link d-flex align-items-center justify-content-center" id="en-tab"
										data-bs-toggle="tab" data-bs-target="#en-tab-content" type="button" role="tab"
										aria-controls="en-tab-content" aria-selected="false">
										EN
									</button>
								</li>
							</ul>

							<!-- Tab panes | Title, Description -->
							<div class="tab-content p-3 border border-top-0 rounded-bottom" id="languageTabsContent">
								<div class="tab-pane fade show active" id="ka-tab-content" role="tabpanel"
									aria-labelledby="ka-tab">
									<div class="mb-3">
										<label for="title_ka" class="form-label">სათაური <span class="text-danger">*</span> </label>
										<input type="text" class="form-control @error('title_ka') is-invalid @enderror" id="title_ka"
											name="title_ka" placeholder="შეიყვანეთ სათაური" value="{{ old('title_ka') }}" required>
										@error('title_ka')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>

									<div class="mb-3">
										<label for="description_ka" class="form-label">აღწერა <span class="text-danger">*</span>
										</label>
										<textarea class="form-control @error('description_ka') is-invalid @enderror"
											id="description_ka" name="description_ka" rows="5" placeholder="შეიყვანეთ აღწერა"
											minlength="10" required>{{ old('description_ka') }}</textarea>
										@error('description_ka')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>

								<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
									<div class="mb-3">
										<label for="title_en" class="form-label">Title <span class="text-danger">*</span> </label>
										<input type="text" class="form-control @error('title_en') is-invalid @enderror" id="title_en"
											name="title_en" placeholder="Enter title" value="{{ old('title_en') }}" required>
										@error('title_en')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
									<div class="mb-3">
										<label for="description_en" class="form-label">Description <span class="text-danger">*</span>
										</label>
										<textarea class="form-control @error('description_en') is-invalid @enderror"
											id="description_en" name="description_en" rows="5" placeholder="Enter description"
											minlength="10" required>{{ old('description_en') }}</textarea>
										@error('description_en')
											<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>
						</div>

						<div class="row mb-4">
							<!-- Image -->
							<div class="col-md-6">
								<label for="project_image" class="form-label">სურათი <span class="text-danger">*</span> </label>
								<input type="file" id="project-image" class="form-control @error('image') is-invalid @enderror"
									name="image" required accept="image/*">
								@error('image')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
								<div class="form-text">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა: 5MB</div>
								<div class="mt-2 text-center">
									<img id="project-image-preview" src="#" alt="Project image preview" class="img-thumbnail d-none"
										style="max-height: 150px;" data-fancybox>
								</div>
							</div>
							<!-- Visibility -->
							<div class="col-md-6">
								<label for="visibility" class="form-label">ხილვადობა <span class="text-danger">*</span> </label>
								<select id="visibility" name="visibility"
									class="form-select @error('visibility') is-invalid @enderror" required>

									<option value="1" {{ old('visibility', '1') == '1' ? 'selected' : '' }}>ხილული</option>
									<option value="0" {{ old('visibility', '1') == '0' ? 'selected' : '' }}>დამალული</option>
								</select>

								@error('visibility')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

						</div>

						<!-- Action buttons -->
						<div class="d-flex justify-content-between align-items-center mt-4">
							<a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
								<i class="bi bi-arrow-left me-2"></i> უკან დაბრუნება
							</a>
							<button type="submit" class="btn btn-primary px-4">
								<i class="bi bi-check-lg me-2"></i> დამატება
							</button>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	<script type="module" src="{{ asset('scripts/project/projectCreate.js') . '?date=' . $modified }}"></script>
@endsection