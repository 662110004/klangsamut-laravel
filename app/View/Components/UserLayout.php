<?php

namespace App\View\Components;

use Closure;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserLayout extends Component
{
    public $categories;
    public $fullwidth;
    /**
     * Create a new component instance.
     */
    public function __construct($fullwidth = false)
    {
        $this->categories = Category::orderBy('name')->get();
        $this->fullwidth = $fullwidth;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-layout');
    }
}
