@extends('layouts.dashboard')
@section('title', 'პარტნიორის რედაქტირება / ' . $partner->title)

@section('main')
   @session('success')
      <div class="alert alert-success" role="alert" x-data="{ show: true }" x-show="show"
        x-init="setTimeout(() => show = false, 3000)">
        {{ $value }}
      </div>
   @endsession

   <div class="row">
      <div class="col-md-12">
        <form method="POST" action="{{ route('partners.update', ['partner' => $partner]) }}"
          enctype="multipart/form-data">
          @csrf
          @method('PUT')

          {{-- Global Error Display --}}
          @if ($errors->any())
           <div class="alert alert-danger">
            <ul class="mb-0">
               @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
            @endforeach
            </ul>
           </div>
         @endif

          <div class="mb-3">
            <input type="text" name="title" value="{{ old('title', $partner->title) }}"
               class="form-control @error('title') is-invalid @enderror" placeholder="სათაური" required>
            @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          </div>

          <div class="mb-3">
            <input type="url" name="link" value="{{ old('link', $partner->link) }}"
               class="form-control @error('link') is-invalid @enderror" placeholder="ლინკი" required>
            @error('link')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          </div>

          <div class="mb-3">
            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
            @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          </div>

          @if ($partner->image)
           <div class="mb-3">
            <a href="{{ asset('images/partners/' . $partner->image) }}" data-fancybox
               data-caption="{{ $partner->title }}">
               <img src="{{ asset('images/partners/' . $partner->image) }}" class="rounded" style="height: 100px;">
            </a>
           </div>
         @endif

          <button type="submit" class="btn btn-primary">განახლება</button>
        </form>
      </div>
   </div>
@endsection

@section('scripts')
   <script>
      Fancybox.bind('[data-fancybox]', {});
   </script>
@endsection