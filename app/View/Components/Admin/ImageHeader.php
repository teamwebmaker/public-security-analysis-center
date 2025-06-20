<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageHeader extends Component
{
    public string $src;
    public string $folder;
    public string $caption;
    public string $height;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $src,
        string $folder,
        string $caption = '',
        string $height = '200px'
    ) {
        $this->src = $src;
        $this->folder = $folder;
        $this->caption = $caption;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.image-header');
    }
}
