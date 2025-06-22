<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;


class AdminCard extends Component
{
    public string $image;
    public string $resourceName;
    public Model $document;
    public ?string $containerClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $image,
        string $resourceName,
        Model $document,
        ?string $containerClass = 'col-xl-4 col-lg-6 mb-4'
    ) {
        $this->image = $image;
        $this->resourceName = $resourceName;
        $this->document = $document;
        $this->containerClass = $containerClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.admin-card');
    }
}