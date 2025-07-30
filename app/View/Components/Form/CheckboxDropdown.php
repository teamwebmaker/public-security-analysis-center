<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;
use Illuminate\Support\Str;

class CheckboxDropdown extends Component
{
    public string $label;
    public $items;
    public string $name;
    public $selected;
    public string $labelField;
    public string $idField;
    public string $dropdownId;

    public function __construct(
        $label,
        $items = [],
        $name = 'items',
        $selected = [],
        $labelField = 'name',
        $idField = 'id',
    ) {
        $this->label = $label;
        $this->items = $items;
        $this->name = $name;
        $this->selected = $selected;
        $this->labelField = $labelField;
        $this->idField = $idField;
        $this->dropdownId = Str::uuid();
    }

    public function render()
    {
        return view('components.form.checkbox-dropdown');
    }
}
