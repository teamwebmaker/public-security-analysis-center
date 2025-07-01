<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmptyStateMessage extends Component
{

    public string $message;
    public bool $overlay; // Determines if the message will have absolute position or not 

    public ?string $minHeight;
    public ?string $resourceName;
    /**
     * Create a new component instance.
     */
    public function __construct(string $message = 'დოკუმენტები ვერ მოიძებნა', bool $overlay = false, ?string $minHeight = '50dvh', ?string $resourceName = null)
    {
        $this->message = $message;
        $this->overlay = $overlay;
        $this->minHeight = $minHeight;
        $this->resourceName = $resourceName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.empty-state-message');
    }
}
