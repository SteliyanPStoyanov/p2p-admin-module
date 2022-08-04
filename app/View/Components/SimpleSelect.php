<?php

namespace App\View\Components;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class SimpleSelect extends Component
{
    public Collection $collection;
    public int $selectedId;
    public string $name;
    public string $key;
    public string $firstOption;
    public string $label;

    /**
     * SelectBranch constructor.
     *
     * @param Collection $collection
     * @param int $selectedId
     * @param string $name
     * @param string $key
     * @param string $firstOption
     * @param string $label
     */
    public function __construct(
        Collection $collection,
        int $selectedId,
        string $name,
        string $key,
        string $firstOption,
        string $label
    ) {
        $this->collection = $collection;
        $this->selectedId = $selectedId;
        $this->name = $name;
        $this->key = $key;
        $this->firstOption = $firstOption;
        $this->label = $label;
    }

    /**
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.simple-select');
    }
}
