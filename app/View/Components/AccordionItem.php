<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AccordionItem extends Component
{
    public string $id;
    public string $label;
    public string $icon;
    public string $parent;

    public bool $open;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $id,
        string $label,
        string $icon = '',
        string $parent = '',
        bool $open = false
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->icon = $icon;
        $this->parent = $parent;
        $this->open = $open;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accordion-item');
    }
}
