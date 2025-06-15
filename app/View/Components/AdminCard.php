<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminCard extends Component
{
    public string $title;
    public string $image;
    public string $editUrl;
    public string $deleteUrl;
    public string $visibility;
    public ?string $description;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $title,
        string $image,
        string $editUrl,
        string $deleteUrl,
        string $visibility,
        ?string $description = null
    ) {
         $this->title = $title;
         $this->image = $image;
         $this->editUrl = $editUrl;
         $this->deleteUrl = $deleteUrl;
         $this->visibility = $visibility;
         $this->description = $description;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view("components.admin-card");
    }
}
