<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GoBackButton extends Component
{
    public string $fallback;
    public string $type;
    public string $text;
    public ?string $size;

    public function __construct(
        string $fallback,
        string $type = "outline-secondary",
        string $text = "უკან დაბრუნება",
        string $size = null
    ) {
        $this->fallback = $fallback;
        $this->type = $type;
        $this->text = $text;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view("components.go-back-button");
    }
}
