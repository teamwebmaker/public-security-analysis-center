<section class="container py-5 px-3 px-sm-5 m-auto" style="max-width: 1100px;">
	<h2 class="gold-text fw-bold mb-4" style="letter-spacing: 0.5px;">
		{{ $item->title->$language }}
	</h2>

	<div class="clearfix">
		<!-- Float only on md and above -->
		<div @class([
			'position-relative d-inline-block mb-3 mb-lg-0 float-lg-end ms-lg-4',
			'overlay-label-wrapper' => $isPdfMarkerDisplayed
		]) @if($isPdfMarkerDisplayed) data-label="{{ __('static.view_document.title') }}"
data-alpha="0.5" @endif>
			@if ($isPdfMarkerDisplayed)
				<a href="#" data-bs-toggle="modal" data-bs-target="#publications-pdfModal" style="display: inline-block;">
					<img src="{{ asset(implode('/', ['images', $category, $item->image])) }}"
						alt="{{ $item->title->$language }}" class="img-fluid rounded-2"
						style="max-width: 450px; width: 100%; height: auto;">
				</a>
			@else
				<img src="{{ asset(implode('/', ['images', $category, $item->image])) }}" alt="{{ $item->title->$language }}"
					class="img-fluid rounded-2" style="max-width: 450px; width: 100%; height: auto;">
			@endif
		</div>
		<div>
			<p class=" fs-5 fw-light justified-text" style="line-height: 1.7; color: #333;">
				{{ $item->description->$language }}
			</p>
		</div>
	</div>
	<!-- Modal for PDF / Document Viewer -->
	<x-modal :id="'publications-pdfModal'" :title="$item->title->$language" size="xl">

		@php
			$file = $item->file ?? $item->document;
			$path = asset('documents/' . $category . '/' . $file);
			$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		@endphp

		{{-- Viewer section --}}
		@if (in_array($extension, ['pdf']))
			{{-- Display PDF directly --}}
			<iframe src="{{ $path }}" class="w-100 border-0" style="height:92%;" allowfullscreen>
			</iframe>

		@elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']))
			{{-- Microsoft Office Online viewer --}}
			<iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($path) }}" class="w-100 border-0"
				style="height:92%;" allowfullscreen>
			</iframe>

		@else
			{{-- No inline viewer available --}}
			<div class="text-center py-5">
				<p class="text-muted mb-3">
					Preview not available for this file type ({{ strtoupper($extension) }}).
				</p>
			</div>
		@endif

		{{-- Always show a download button --}}
		<div class="text-center py-2">
			<a href="{{ $path }}" class="btn btn-sm btn-outline-primary" download>
				<i class="fa fa-download me-1"></i> {{ __('static.view_document.download') }}
			</a>
		</div>

	</x-modal>
</section>