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

    public function __construct(Collection|LengthAwarePaginator $items, ?string $containerClass = 'row row-cols-1 row-cols-sm-2 row-cols-lg-3 ')
    {
        $this->items = $items;
        $this->containerClass = $containerClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.admin.index-view');
    }
}
