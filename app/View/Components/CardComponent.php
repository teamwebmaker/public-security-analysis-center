<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardComponent extends Component
{
    public ?string $title;
    public ?string $description;
    public ?string $image;
    public ?string $imageLegacyDirectory;
    public ?string $link;
    public ?string $date;

    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $image = null,
        ?string $imageLegacyDirectory = null,
        ?string $link = null,
        ?string $date = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->imageLegacyDirectory = $imageLegacyDirectory;
        $this->link = $link;
        $this->date = $date;
    }

    public function render(): View|Closure|string
    {
        return view("components.card-component");
    }
}
