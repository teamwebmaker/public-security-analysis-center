<?php

namespace App\View\Components\Auth;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthForm extends Component
{
    public string $route;
    public string $type;

    // Separator option: 'or' | 'line' | 'none'
    public string $separator;

    /**
     * Create a new component instance.
     */
    public function __construct(string $route, string $type, string $separator = 'or')
    {

        $this->route = $route;
        $this->type = $type;
        // enforce enum-like behavior
        $allowed = ['or', 'line', 'none'];
        $this->separator = in_array($separator, $allowed, true) ? $separator : 'or';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.auth.auth-form');
    }
}
