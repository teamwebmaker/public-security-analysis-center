<?php

namespace App\View\Components\Shared;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Table extends Component
{
    public $headers;
    public $rows;
    public Collection|LengthAwarePaginator $items;
    public $resourceName;
    public $actions;
    public array $tooltipColumns;


    /**
     * Create a new component instance.
     */
    public function __construct($headers, $rows, $items, $resourceName, $actions = false, $tooltipColumns = [])
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->items = $items;
        $this->resourceName = $resourceName;
        $this->actions = $actions;
        $this->tooltipColumns = $tooltipColumns;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.shared.table');
    }
}
