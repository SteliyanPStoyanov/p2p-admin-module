<?php

namespace App\View\Components;

class BtnExportToPdf extends Btn
{
    /**
     * @return \Illuminate\View\View|string|void
     */
    public function render()
    {
        return view('components.btn-export-to-pdf');
    }
}
