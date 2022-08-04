<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Btn extends Component
{
    public $url;
    public $btnName;

    /**
     * Create a new component instance.
     *
     * @param string $url
     * @param string $name
     */
    public function __construct(string $url = '', string $name = '')
    {
        $this->url = $url;
        $this->btnName = $name;
    }

    public function render() {}
}
