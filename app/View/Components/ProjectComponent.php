<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProjectComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public string $language;
    public object $project;
    public function __construct($project, $language)
    {
        $this -> language = $language;
        $this -> project = $project;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.project-component');
    }
}
