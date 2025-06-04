<?php
namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public string $id;
    public string $title;
    public string $size;
    public string $height;

    /**
     * Create a new component instance.
     */
    public function __construct(string $id, string $title = '', string $size = 'xl', string $height = '80dvh')
    {
        $this->id = $id;
        $this->title = $title;
        $this->size = $size;
        $this->height = $height;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal');
    }
}
