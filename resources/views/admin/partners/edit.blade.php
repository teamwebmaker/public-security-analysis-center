@extends('layouts.dashboard')
@section('title', 'პარტნიორის რედაქტირება / ' . $partner->title)

@section('main')
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<!-- Global Success Message -->
			@session('success')
				<div class="alert alert-success alert-dismissible fade show mb-4" role="alert" x-data="{ show: true }"
					x-show="show" x-init="setTimeout(() => show = false, 5000)">
					<i class="bi bi-check-circle-fill me-2"></i> {{ $value }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endsession

			<div class="card border-0 shadow-sm mb-4">
				<!-- Existing Image Hero Display -->
				@if($partner->image)
					<div class="card-header bg-transparent border-0 text-center position-relative p-0" style="min-height: 180px;">
						<a href="{{ asset('images/partners/' . $partner->image) }}" data-fancybox data-caption="ძველი სურათი"
							class="d-block w-100 h-100">
							<img src="{{ asset('images/partners/' . $partner->image) }}"
								class="img-fluid rounded-top w-100 h-100 object-fit-cover"
								style="max-height: 200px; object-position: center;">
						</a>
					</div>
				@endif

				<div class="card-body">
					<form method="POST" action="{{ route('partners.update', ['partner' => $partner]) }}"
						enctype="multipart/form-data" class="needs-validation" novalidate>
						@csrf
						@method('PUT')
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
							<h1 class="h4">პარტნიორის რედაქტირება</h1>
						</div>

						<!-- Title Field -->
						<div class="mb-4">
							<label for="title" class="form-label d-flex align-items-center gap-2">
								<span>სათაური <span class="text-danger">*</span></span>
							</label>
							<input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
								value="{{ old('title', $partner->title) }}" placeholder="შეიყვანეთ სათაური" required>
							@error('title')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<!-- Link Field -->
						<div class="mb-4">
							<label for="link" class="form-label d-flex align-items-center gap-2">
								<span>ლინკი <span class="text-danger">*</span></span>
							</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
								<input type="url" id="link" name="link" class="form-control @error('link') is-invalid @enderror"
									value="{{ old('link', $partner->link) }}" placeholder="https://example.com" required>
								@error('link')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="row mb-4">
							<!-- Image Upload Section -->
							<div class="col-md-6">
								<label for="image" class="form-label">ახალი სურათი</label>

								<input type="file" id="partner-image" name="image"
									class="form-control @error('image') is-invalid @enderror"
									accept="image/jpeg,image/png,image/webp">
								<small class="form-text text-muted">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა:
									5MB</small>
								<div class="mt-2 text-center">
									<img id="partner-image-preview" src="#" alt="Image preview" class="img-thumbnail d-none"
										style="max-height: 150px;" data-fancybox>
								</div>
								@error('image')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<!-- Visibility Section -->
							<div class="col-md-6">
								<label for="visibility" class="form-label">ხილვადობა <span class="text-danger">*</span></label>
								<div class="input-group">
									<select id="visibility" name="visibility"
										class="form-select @error('visibility') is-invalid @enderror" required>
										<option value="1" {{ old('visibility', $partner->visibility ?? '1') == '1' ? 'selected' : ''
											   }}>
											ხილული
										</option>
										<option value="0" {{ old('visibility', $partner->visibility ?? '1') == '0' ? 'selected' : ''
											   }}>
											დამალული
										</option>
									</select>
								</div>
								@error('visibility')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<!-- Action Buttons -->
						<div class="d-flex justify-content-between align-items-center flex-column flex-sm-row gap-2 mt-4">
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
	{!! load_script('scripts/partner.js') !!}
@endsection