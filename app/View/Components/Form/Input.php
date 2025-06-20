<?php

namespace App\View\Components\Form;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public string $id;
    public string $name;
    public string $type;
    public string $class;
    public $value;
    public bool $required;
    public string $placeholder;
    public ?string $label;

    public ?string $icon;
    public string $accept;

    public function __construct(
        string $name = '',
        string $type = 'text',
        ?string $id = null,
        string $class = '',
        $value = null,
        bool $required = true,
        string $placeholder = '',
        ?string $label = null,
        ?string $icon = null,
        string $accept = 'image/jpeg,image/png,image/webp'
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->id = $id ?? $name;
        $this->class = 'form-control ' . $class;
        $this->value = $value;
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->icon = $icon;
        $this->accept = $accept;

        // Handle old input
        if ($name && !$value) {
            $this->value = old($name);
        }
    }

    public function render(): View
    {
        return view('components.form.input');
    }
}