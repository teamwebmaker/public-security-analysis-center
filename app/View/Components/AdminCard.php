<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;


class AdminCard extends Component
{
    public Model $document;

    public string $title;
    public string $resourceName;
    public ?string $image;
    public ?string $containerClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        Model $document,
        string $title,
        string $resourceName,
        ?string $image,
        ?string $containerClass = 'col-xl-4 col-lg-6 mb-4'
    ) {
        $this->image = $image;
        $this->title = $title;
        $this->document = $document;
        $this->resourceName = $resourceName;
        $this->containerClass = $containerClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.admin-card');
    }
}