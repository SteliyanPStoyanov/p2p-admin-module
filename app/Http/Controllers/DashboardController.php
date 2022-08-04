<?php

namespace App\Http\Controllers;

use Modules\Core\Controllers\BaseController;

class DashboardController extends BaseController
{
    protected string $pageTitle = 'Dashboard';

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
}
