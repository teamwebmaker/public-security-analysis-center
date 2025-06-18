@extends('layouts.dashboard')
@section('title', 'პროექტის შექმნა')
@section('main')
	<div class="row justify-content-center">
		<div class="col-lg-9">
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
										<x-form.input name="title_ka" label="სათაური" placeholder="შეიყვანეთ სათაური"
											value="{{ old('title_ka') }}" />
									</div>

									<div class="mb-3">
										<x-form.textarea name="description_ka" label="აღწერა" placeholder="შეიყვანეთ აღწერა"
											value="{{ old('description_ka') }}" />
									</div>
								</div>

								<div class="tab-pane fade" id="en-tab-content" role="tabpanel" aria-labelledby="en-tab">
									<div class="mb-3">
										<x-form.input name="title_en" label="Title" placeholder="Enter title"
											value="{{ old('title_en') }}" />
									</div>
									<div class="mb-3">
										<x-form.textarea name="description_en" label="Description" placeholder="Enter description"
											value="{{ old('description_en') }}" />
									</div>
								</div>
							</div>
						</div>

						<div class="row mb-4">
							<!-- Image -->
							<div class="col-md-6">
								<x-form.input type="file" id="project-image" name="image" label="სურათი"
									placeholder="შეიყვანეთ სურათი" />
								<x-form.image-upload-preview id="project" />

							</div>
							<!-- Visibility -->
							<div class="col-md-6">
								<x-form.select name="visibility" :options="['1' => 'ხილული', '0' => 'დამალული']" selected="1"
									label="ხილვადობა" required class="custom-class" />
							</div>

							<!-- Action buttons -->
							<div class="d-flex justify-content-between align-items-center flex-column flex-sm-row gap-2 mt-4">
								<x-go-back-button fallback="projects.index" />
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
	{!! load_script('scripts/project/projectCreate.js') !!}
@endsection