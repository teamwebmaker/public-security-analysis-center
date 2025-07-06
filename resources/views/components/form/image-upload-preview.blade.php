<!-- 
    Preview container for uploaded image. 
    Works with imageUploader.js, which sets the preview 
    after a file is selected via input.
-->
<div class="{{ $containerClass }}">
    <img id="{{ $id }}-preview" src="#" alt="{{ $id }} {{ $alt }}" class="{{ $class }}" style="{{ $style }}"
        data-fancybox>
</div>