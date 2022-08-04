<?php

namespace App\View\Components;

class BtnCopyUrl extends Btn
{
    /**
     * @var string
     */
    public $urlGetData;

    public function __construct(string $url = '', string $name = '', $urlGetData = '')
    {
        parent::__construct($url, $name);
        $this->urlGetData = $urlGetData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.btn-copy-url');
    }
}
