<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PartnerComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public object $partner;
    public function __construct($partner)
    {
        $this -> partner = $partner;

    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.partner-component');
    }
}
