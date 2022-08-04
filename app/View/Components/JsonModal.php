<?php

namespace App\View\Components;

use Illuminate\View\Component;

class JsonModal extends Component
{
    public $id;
    public $buttonLabel;
    public $items;

    public function __construct($id, $buttonLabel, $items)
    {
        $this->id = $id;
        $this->buttonLabel = $buttonLabel;
        $this->items = $items;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.json-modal');
    }
}
