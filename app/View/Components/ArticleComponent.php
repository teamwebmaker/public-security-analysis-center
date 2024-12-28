<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ArticleComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public string $language;
    public object $article;
    public function __construct($article, $language)
    {
        $this -> language = $language;
        $this -> article = $article;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.article-component');
    }
}



