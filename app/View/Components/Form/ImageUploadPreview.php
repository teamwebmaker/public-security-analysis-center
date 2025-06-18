<?php

namespace App\View\Components\Form;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageUploadPreview extends Component
{
    public string $id;
    public string $alt;
    public string $class;
    public string $containerClass;
    public string $style;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        string $alt = 'image preview',
        string $class = '',
        string $containerClass = '',
        string $style = 'max-height: 150px;'
    ) {
        $this->id = $id;
        $this->alt = $alt;
        $this->class = 'img-thumbnail d-none ' . $class;
        $this->containerClass = 'mt-2 text-center ' . $containerClass;
        $this->style = $style;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.form.image-upload-preview');
    }
}