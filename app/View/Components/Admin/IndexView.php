<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;


class IndexView extends Component
{

    public Collection|LengthAwarePaginator $items;
    public ?string $containerClass;

    public ?string $resourceName;
    public ?bool $hasSpeedDial;

    public function __construct(Collection|LengthAwarePaginator $items, ?string $resourceName = null, ?string $containerClass = 'position-relative row row-cols-1 row-cols-sm-2 row-cols-lg-3', ?bool $hasSpeedDial = true)
    {
        $this->items = $items;
        $this->hasSpeedDial = $hasSpeedDial;
        $this->resourceName = $resourceName;
        $this->containerClass = $containerClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.admin.index-view');
    }
}
