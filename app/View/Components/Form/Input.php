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
    public bool $isImage;

    public string $placeholder;
    public string $accept;

    public ?string $label;

    public ?string $icon;
    public ?string $iconPosition;


    public ?int $minlength = null;
    public ?int $maxlength = null;

    public function __construct(
        string $name = '',
        string $type = 'text',
        ?string $id = null,
        string $class = '',
        $value = null,
        bool $required = true,
        bool $isImage = true,
        string $placeholder = '',
        ?string $label = null,
        ?string $icon = null,
        ?string $iconPosition = 'left',
        string $accept = 'image/jpeg,image/png,image/webp'
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->id = $id ?? $name;
        $this->class = 'form-control ' . $class;
        $this->value = $value ?? old($name);
        $this->required = $required;
        $this->isImage = $isImage;
        $this->placeholder = $placeholder;
        $this->label = $label;
        $this->icon = $icon;
        $this->iconPosition = $iconPosition;
        $this->accept = $accept;

        // Set minlength for text input
        if ($type === 'text') {
            $this->minlength = 3;   // default for text
            $this->maxlength = 200; // default for text ( 0 and null means no limit )
        }
    }

    public function render(): View
    {
        return view('components.form.input');
    }
}
