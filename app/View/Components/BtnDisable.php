<?php

namespace App\View\Components;

class BtnDisable extends Btn
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.btn-disable');
    }
}
