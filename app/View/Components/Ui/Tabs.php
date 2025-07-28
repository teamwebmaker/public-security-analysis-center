<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tabs extends Component
{
    public string $contentContainerClass;
    /**
     * Create a new component instance.
     */
    public function __construct($contentContainerClass = 'tab-content p-3 border border-top-0 rounded-bottom')
    {
        $this->contentContainerClass = $contentContainerClass;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.tabs');
    }
}
