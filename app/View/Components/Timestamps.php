<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Modules\Core\Models\BaseModel;

class Timestamps extends Component
{
    public BaseModel $model;

    /**
     * Create a new component instance.
     *
     * @param BaseModel $model
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.timestamps');
    }
}
