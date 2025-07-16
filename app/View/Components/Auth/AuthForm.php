<?php

namespace App\View\Components\Auth;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthForm extends Component
{
    public string $route;
    public string $type;

    /**
     * Create a new component instance.
     */
    public function __construct(string $route, string $type)
    {

        $this->route = $route;
        $this->type = $type;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.auth.auth-form');
    }
}
