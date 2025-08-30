<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SortData extends Component
{
    public string $name;
    public array $options;
    public string $selected;
    public string $class;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        array $options = [],
        string $selected = 'newest',
        string $class = ''
    ) {
        $this->name = $name;
        $this->options = $options ?: [
            'newest' => __('static.sort.newest'),
            'oldest' => __('static.sort.oldest'),
        ];
        $this->selected = $selected;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sort-data');
    }
}
