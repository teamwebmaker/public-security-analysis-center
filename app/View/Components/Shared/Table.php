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
    public $deleteMessage;
    public ?string $resourceName;
    public array $tooltipColumns;
    public ?array $sortableMap;

    public int $action_delete;

    public ?Closure $customActions;
    public ?Closure $modalTriggers;
    /**
     * Create a new component instance.
     */
    public function __construct($headers, $rows, $items, $resourceName = null, $actions = false, $deleteMessage = 'ნამდვილად გსურთ წაშლა?', $tooltipColumns = [], $sortableMap = [], $action_delete = true, $customActions = null, $modalTriggers = null)
    {
        $this->headers = $headers;
        $this->rows = $rows;
        $this->items = $items;
        $this->actions = $actions;
        $this->deleteMessage = $deleteMessage;
        $this->sortableMap = $sortableMap;
        $this->resourceName = $resourceName;
        $this->tooltipColumns = $tooltipColumns;
        $this->action_delete = $action_delete;
        $this->customActions = $customActions;
        $this->modalTriggers = $modalTriggers;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.shared.table');
    }
}
