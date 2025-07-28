<?php

namespace App\View\Components\Admin\Dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DashboardCard extends Component
{
    public string $icon;
    public string $title;
    public int|string $count;
    public string $viewRoute;
    public ?string $createRoute;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $icon,
        string $title,
        int|string $count,
        string $viewRoute,
        string $createRoute = null
    ) {
        $this->icon = $icon;
        $this->title = $title;
        $this->count = $count;
        $this->viewRoute = $viewRoute;
        $this->createRoute = $createRoute;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.dashboard.dashboard-card');
    }
}
