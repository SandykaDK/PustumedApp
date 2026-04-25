<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SortableTh extends Component
{
    public string $column;
    public string $label;
    public ?string $route;

    /**
     * Create a new component instance.
     *
     * @param string $column
     * @param string $label
     * @param string|null $route  Optional named route to generate the sort URL (e.g. 'users.index')
     */
    public function __construct(string $column, string $label, ?string $route = null)
    {
        $this->column = $column;
        $this->label = $label;
        $this->route = $route;
    }

    public function render()
    {
        return view('components.sortable-th');
    }
}
