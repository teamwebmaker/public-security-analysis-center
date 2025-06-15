@extends('layouts.dashboard')
@section('title', 'პარტნიორის შექმნა')
@section('main')
	<div class="row justify-content-center">
		<div class="col-lg-9">
			<div class="card border-0 shadow-sm">
				<div class="card-body p-4">
					<h1 class="h4 mb-4">პარტნიორის შექმნა</h1>

					<form method="POST" action="{{ route('partners.store') }}" enctype="multipart/form-data"
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

						<!-- partner title -->
						<div class="mb-4">
							<label for="title" class="form-label">სათაური <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
								value="{{ old('title') }}" placeholder="შეიყვანეთ სათაური" required>
							@error('title')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<!-- partner link -->
						<div class="mb-4">
							<label for="link" class="form-label">ლინკი <span class="text-danger">*</span></label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
								<input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link"
									value="{{ old('link') }}" placeholder="https://example.com" required>
								@error('link')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="row mb-4">
							<!-- Image -->
							<div class="col-md-6">
								<label for="image" class="form-label">სურათი <span class="text-danger">*</span></label>
								<input type="file" id="partner-image" class="form-control @error('image') is-invalid @enderror"
									name="image" required accept="image/jpeg,image/png,image/webp">
								@error('image')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
								<div class="form-text">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა: 5MB</div>
								<div class="mt-2 text-center">
									<img id="partner-image-preview" src="#" alt="Project image preview" class="img-thumbnail d-none"
										style="max-height: 150px;" data-fancybox>
								</div>
							</div>
							<!-- Visibility -->
							<div class="col-md-6">
								<label for="visibility" class="form-label">ხილვადობა <span class="text-danger">*</span></label>
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
						<div class="d-flex justify-content-between align-items-center flex-column flex-sm-row gap-2 mt-4">
							<x-go-back-button fallback="partners.index" />
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
	<script type="module" src="{{ asset('scripts/partner.js') . '?date=' . $modified }}"></script>
@endsection