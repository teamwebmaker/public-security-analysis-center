<?php

namespace App\View\Components\Management;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCard extends Component
{
    public string $label;
    public string|int $count;
    public string $icon;
    public string $iconWrapperClasses;
    public string $iconWrapperStyle;
    public string $iconStyle;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $label,
        $count,
        $icon,
        $iconWrapperClasses = '',
        $iconWrapperStyle = '',
        $iconStyle = ''
    ) {
        $this->label = $label;
        $this->count = $count;
        $this->icon = $icon;
        $this->iconWrapperClasses = $iconWrapperClasses;
        $this->iconWrapperStyle = $iconWrapperStyle;
        $this->iconStyle = $iconStyle;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.management.stat-card');
    }
}
