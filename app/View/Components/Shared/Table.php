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
    public $items;
    public $actions;
    public ?string $resourceName;
    public array $tooltipColumns;
    public ?array $sortableMap;
    /**
     * Create a new component instance.
     */
    public function __construct($headers, $rows, $items, $resourceName = null, $actions = false, $tooltipColumns = [], $sortableMap = [], )
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->items = $items;
        $this->actions = $actions;
        $this->sortableMap = $sortableMap;
        $this->resourceName = $resourceName;
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
