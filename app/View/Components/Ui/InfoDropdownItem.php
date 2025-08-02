<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Collection;
class InfoDropdownItem extends Component
{

    public string $label;
    public string $icon;
    public string $name;
    public Collection $items;
    public Closure $getItemText;
    /**
     * Create a new component instance.
     */
    public function __construct(string $label, string $icon, string $name, Collection $items, Closure $getItemText)
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->name = $name;
        $this->items = $items;
        $this->getItemText = $getItemText;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components..ui.info-dropdown-item');
    }
}
