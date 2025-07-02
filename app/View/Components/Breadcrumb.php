<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    /**
     * Create a new component instance.
     */
    public $formattedSlug;
    public $category;
    public $main;
    public $route;
    
    public function __construct($slug = 'News', $category = false, $main = '',$route = '#')
    {
        $this->formattedSlug = ucwords(str_replace('_', ' ', $slug));
        $this->category = $category;
        $this->main = $main;
        $this->route = $route;
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}
