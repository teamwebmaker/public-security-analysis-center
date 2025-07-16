<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AccordionNavItem extends Component
{
    public string $id;
    public string $icon;
    public string $label;
    public array $routes;
    public array $activeRoutes;
    public ?string $parent;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        string $icon,
        string $label,
        array $routes = [],
        array $activeRoutes = [],
        string $parent = null
    ) {
        //
        $this->id = $id;
        $this->icon = $icon;
        $this->label = $label;
        $this->routes = $routes;
        $this->activeRoutes = $activeRoutes;
        $this->parent = $parent;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accordion-nav-item');
    }
}
