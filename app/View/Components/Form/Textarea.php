<?php

namespace App\View\Components\Form;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public string $id;
    public string $name;
    public string $class;
    public $value;
    public bool $required;
    public string $placeholder;
    public ?string $label;
    public int $rows;
    public ?int $minlength;

    public function __construct(
        string $name = '',
        ?string $id = null,
        string $class = '',
        $value = '',
        bool $required = true,
        string $placeholder = '',
        ?string $label = null,
        int $rows = 5,
        ?int $minlength = 10
    ) {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->class = 'form-control ' . $class;
        $this->value = old($name, $value);
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->rows = $rows;
        $this->minlength = $minlength;
    }

    public function render(): View
    {
        return view('components.form.textarea');
    }
}