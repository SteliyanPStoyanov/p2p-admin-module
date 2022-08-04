<?php


namespace Modules\Profile\Http\Controllers;

use Modules\Common\Services\InvestorService;
use Modules\Core\Controllers\BaseController;

class ReferralController extends BaseController
{
    protected InvestorService $investorService;

    public function __construct(InvestorService $investorService)
    {
        $this->investorService = $investorService;
        parent::__construct();
    }

    public function referralLink($hash)
    {
        try {
            if ($this->investorService->checkHashExist($hash) == false) {
                return redirect()->route('profile.login')->with('fail', __('common.NotExistHash'));
            }

            $parentInvestor = $this->investorService->getByHash($hash);

            return view('profile::register.index', compact('parentInvestor'));
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }
}
