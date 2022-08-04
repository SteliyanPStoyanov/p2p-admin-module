<?php

namespace Modules\Profile\Http\Controllers;

use Modules\Core\Controllers\BaseController;

class HelpController extends BaseController
{
    public function __construct() {

        parent::__construct();
    }

    public function index()
    {
        try {
            return view(
                'profile::help.index',
                [
                    'investor' => $this->getInvestor(),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }
}
