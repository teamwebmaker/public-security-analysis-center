<?php

namespace App\View\Components\Admin\Services;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $id;
    public string $name;
    public string $class;
    public $options;
    public $selected;
    public bool $required;
    public ?string $label;
    public function __construct(
        string $name,
        $options = [],
        $selected = null,
        ?string $id = null,
        string $class = '',
        bool $required = false,
        ?string $label = null
    ) {
        $this->name = $name;
        $this->options = $options;
        $this->selected = old($name, $selected);
        $this->id = $id ?? $name;
        $this->class = 'form-select ' . $class;
        $this->required = $required;
        $this->label = $label;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.services.select');
    }
}
