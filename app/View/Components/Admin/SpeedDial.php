<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SpeedDial extends Component
{
    public string $resourceName;
    public ?string $isCreate = null;

    public function __construct(string $resourceName, ?string $isCreate = null)
    {
        $this->resourceName = $resourceName;
        $this->isCreate = $isCreate;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.speed-dial');
    }
}
