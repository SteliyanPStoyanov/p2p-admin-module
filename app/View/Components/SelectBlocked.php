<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectBlocked extends Component
{
    public $blocked;

    public function __construct($blocked)
    {
        $this->blocked = $blocked;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.select-blocked');
    }
}
