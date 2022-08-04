<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ButtonBottomBar extends Component
{
    public $url;
    public $btnSaveEditName;
    public $btnCancelName;

    /**
     * Create a new component instance.
     *
     * @param string $url
     * @param string $saveEditName
     * @param string $cancelName
     */
    public function __construct(string $url = '', string $saveEditName = '', string $cancelName = '')
    {
        $this->url = $url;
        $this->btnSaveEditName = $saveEditName;
        $this->btnCancelName = $cancelName;
    }

    public function render()
    {
        return view('components.button-bottom-bar');

    }
}
