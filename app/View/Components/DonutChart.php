<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DonutChart extends Component
{

    public $id;
    public $data;

    /**
     * Create a new component instance.
     */
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

        return view('components.donut-chart', compact('data', 'id'));
    }
}
