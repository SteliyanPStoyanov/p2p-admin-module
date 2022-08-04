<?php

namespace Modules\Profile\Http\Controllers;

use Auth;
use Illuminate\Support\Carbon;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Events\RestorePassword;
use Modules\Common\Services\BlockedIpService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\RegistrationAttemptService;
use Modules\Common\Services\RestoreHashService;
use Modules\Core\Controllers\BaseController;
use Modules\Profile\Http\Requests\ForgottenPasswordRequest;
use Modules\Profile\Http\Requests\RestorePasswordRequest;

class ForgotPasswordController extends BaseController
{
    protected RegistrationAttemptService $registrationAttemptService;
    protected BlockedIpService $blockedIpService;
    protected RestoreHashService $restoreHashService;
    protected InvestorService $investorService;

    public function __construct(
        RegistrationAttemptService $registrationAttemptService,
        BlockedIpService $blockedIpService,
        RestoreHashService $restoreHashService,
        InvestorService $investorService
    ) {
        $this->registrationAttemptService = $registrationAttemptService;
        $this->blockedIpService = $blockedIpService;
        $this->restoreHashService = $restoreHashService;
        $this->investorService = $investorService;
        parent::__construct();
    }

    public function forgotPasswordForm()
    {
        try {
            return view('profile::forgot-password.index');
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    public function forgotPassword(ForgottenPasswordRequest $request)
    {
        $validated = $request->validated();

        try {
            $device = $request->header('User-Agent');
            $ip = $request->ip();

            $blockedTill = $this->blockedIpService->blockedTill($ip, BlockedIp::BLOCKED_IP_REASON_FORGOT_PASSWORD);

            if ($blockedTill) {
                return redirect()->route('profile')->with(
                    'fail',
                    __(
                        'auth.throttleForgotPassword',
                        [
                            'time' => $blockedTill->blocked_till
                        ]
                    )
                );
            }

            if ($this->registrationAttemptService->attemptCount(
                    $ip,
                    BlockedIp::BLOCKED_IP_REASON_FORGOT_PASSWORD
                ) == true) {
                return redirect()->route('profile')->with(
                    'fail',
                    __(
                        'auth.throttleForgotPassword',
                        [
                            'time' => Carbon::now()->addDay(1)
                        ]
                    )
                );
            }

            $this->registrationAttemptService->create($validated['email'], $device, $ip);


            if ($this->investorService->emailExist($validated['email']) == false) {
                return redirect()->route('profile.forgotPassword')->with('fail', __('common.UserEmailNotExists'));
            }

            $investor = $this->investorService->getByEmail($validated['email']);

            event(new RestorePassword($investor));

            return redirect()->route('profile.forgotPassword')->with('success', __('common.PasswordRecoveryDescription'));
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param string $hash
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function restorePasswordForm(string $hash)
    {
        try {
            if ($this->restoreHashService->checkHashExist($hash) == false) {
                return redirect()->route('profile.forgotPassword')->with('fail', __('common.NotExistHash'));
            }

            if ($this->restoreHashService->isUsedHash($hash) == false) {
                return redirect()->route('profile.forgotPassword')->with('fail', __('common.WrongHash'));
            }

            return view('profile::forgot-password.restore', compact('hash'));
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    public function restorePassword(RestorePasswordRequest $request)
    {
        $validated = $request->validated();

        try {
            if ($this->restoreHashService->checkHashExist($validated['hash']) == false) {
                return redirect()->route('profile.forgotPassword')->with('fail', __('common.NotExistHash'));
            }
            if ($this->restoreHashService->isUsedHash($validated['hash']) == false) {
                return redirect()->route('profile.forgotPassword')->with('fail', __('common.WrongHash'));
            }

            $restoreHash = $this->restoreHashService->getHash($validated['hash']);

            $this->investorService->restorePassword($restoreHash->investor_id, $validated['password']);

            $this->restoreHashService->useHash($validated['hash']);

            return redirect()->route('profile')->with('success', __('common.PasswordChangeSuccess'));
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }
}
