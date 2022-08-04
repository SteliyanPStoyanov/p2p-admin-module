<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectSex extends Component
{
    public $sex;
    public $componentNameCustom;

    public function __construct(string $sex = '', string $componentName = '')
    {
        $this->sex = $sex;
        $this->componentNameCustom = !empty($componentName) ? $componentName : 'sex';
    }

    /**
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.select-sex');
    }
}
