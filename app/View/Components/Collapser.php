<?php

namespace App\View\Components;

class Collapser extends Btn
{
    public bool $showSelectAll;

    /**
     * Collapser constructor.
     *
     * @param string $url
     * @param string $name
     * @param bool $showSelectAll
     */
    public function __construct(
        string $url = '',
        string $name = '',
        bool $showSelectAll = true
    ) {
        parent::__construct($url, $name);
        $this->showSelectAll = $showSelectAll;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.colapser');
    }
}
