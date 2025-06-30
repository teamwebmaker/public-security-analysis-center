<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toast extends Component
{

    public array $messages;

    public string $type = 'success';
    /**
     * Create a new component instance.
     */
    public function __construct(array $messages, string $type = 'success')
    {

        $this->messages = $messages;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.toast');
    }
}
