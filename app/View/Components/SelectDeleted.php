<?php


namespace App\View\Components;


class SelectDeleted extends \Illuminate\View\Component
{
    public $deleted;

    public function __construct($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.select-deleted');
    }
}
