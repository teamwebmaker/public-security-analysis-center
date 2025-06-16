<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AccordionItem extends Component
{
    public string $id;
    public string $icon;
    public string $label;
    public array $routes;
    public array $activeRoutes;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        string $icon,
        string $label,
        array $routes = [],
        array $activeRoutes = []
    ) {
        $this->id = $id;
        $this->icon = $icon;
        $this->label = $label;
        $this->routes = $routes;
        $this->activeRoutes = $activeRoutes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accordion-item');
    }
}
