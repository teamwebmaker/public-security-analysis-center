<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SingleItemComponent extends Component
{
    public string $language;
    public object $item;
    public string $category;

    public function __construct($item, $language, $category)
    {
        $this -> language = $language;
        $this -> item = $item;
        $this -> category = $category;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.single-item-component');
    }
}
