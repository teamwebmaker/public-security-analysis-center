<section class="container py-5 px-3 px-sm-5 m-auto" style="max-width: 1100px;">
	<h2 class="gold-text fw-bold mb-4" style="letter-spacing: 0.5px;">
		{{ $item->title->$language }}
	</h2>

	<div class="clearfix">
		<!-- Float only on md and above -->
		{{--TODO: View Pdf should be variable for KA/EN --}}
		<div @class([
			'position-relative d-inline-block mb-3 mb-lg-0 float-lg-end ms-lg-4',
			'publication-img-wrapper' => $isPdfMarkerDisplayed
		]) @if($isPdfMarkerDisplayed) data-pdfMarker="View pdf" @endif>
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
	<!-- Modal for PDF Viewer -->
	<x-modal :id="'publications-pdfModal'" :title=" $item->title->$language " size="xl">
		<iframe src="{{ asset('documents/' . $category . '/' . $item->file) }}" class="w-100 h-100 border-0"
			allowfullscreen></iframe>
	</x-modal>
</section>