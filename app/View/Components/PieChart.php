<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;

class PieChart extends Component
{
    /**
     * Create a new component instance.
     */

    public $id;
    public $data;

    public function __construct($data, $id)
    {
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data = $this->data;
        $id = $this->id;

        return view('components.pie-chart', compact('data', 'id'));
    }
}
