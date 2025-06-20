<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CheckboxGroup extends Component
{
    public string $name;
    public string $label;
    public array $options;
    public array $selected;
    public string $class;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        array $options,
        string $label = "",
        array $selected = [],
        string $class = "d-flex flex-wrap gap-2"
    ) {
        //
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = old($name, $selected);
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view("components.form.checkbox-group");
    }
}
