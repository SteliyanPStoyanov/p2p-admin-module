<?php

namespace Modules\Profile\Http\Controllers;

use Auth;
use Illuminate\Support\Carbon;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Services\BlockedIpService;
use Modules\Common\Services\InvestorLoginLogService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\LoginAttemptService;
use Modules\Core\Controllers\BaseController;
use Modules\Profile\Http\Requests\LoginRequest;
use ReCaptcha\ReCaptcha;
use Throwable;

class LoginController extends BaseController
{
    protected InvestorLoginLogService $investorLoginLogService;
    protected LoginAttemptService $loginAttemptService;
    protected BlockedIpService $blockedIpService;
    protected InvestorService $investorService;

    public function __construct(
        InvestorLoginLogService $investorLoginLogService,
        LoginAttemptService $loginAttemptService,
        BlockedIpService $blockedIpService,
        InvestorService $investorService
    ) {
        $this->investorLoginLogService = $investorLoginLogService;
        $this->loginAttemptService = $loginAttemptService;
        $this->blockedIpService = $blockedIpService;
        $this->investorService = $investorService;
        parent::__construct();
    }

    public function loginFrom()
    {
        return view('profile::login.index');
    }

    /**
     * @param LoginRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    protected function login(LoginRequest $request)
    {
        $validated = $request->validated();

        try {
            $validated['email'] = strtolower(trim($validated['email']));


            if (isProd()) {
                $response = (new ReCaptcha(config('app.recaptcha_secret')))
                    ->setExpectedAction('submit')
                    ->setScoreThreshold(config('app.recaptcha_score_threshold'))
                    ->verify($request->input('g-recaptcha-response'), $_SERVER['REMOTE_ADDR']);

                if (!$response->isSuccess()) {
                    abort(403);
                }
            }


            // check if IP is blocked
            $ip = $request->ip();
            $device = $request->header('User-Agent');
            $blockedTill = $this->blockedIpService->blockedTill(
                $ip,
                BlockedIp::BLOCKED_IP_REASON_LOGIN
            );
            if ($blockedTill) {
                return redirect()->route('profile')->with(
                    'fail',
                    __(
                        'auth.throttleLogin',
                        [
                            'time' => $blockedTill->blocked_till
                        ]
                    ),
                );
            }
            // attempt to login
            $attempt = [
                'email' => $validated['email'],
                'password' => $validated['password'],
                'active' => 1,
            ];
            if (Auth::guard('investor')->attempt($attempt)) {
                try {
                    $investor = $this->getInvestor();

                    $this->investorLoginLogService->create(
                        $device,
                        $investor->investor_id,
                        $investor->newDeviceNotificationChecked()
                    );
                } catch (Throwable $e) {
                    abort(403);
                }

                return redirect()->route('profile.dashboard.overview');
            }


            // if email exists but password is wrong, we ncrease wrong attempts and block investor if he did to many attempts
            if (true === $this->investorService->emailExist($validated['email'])) {

                if (true === $this->loginAttemptService->isAttemptCountExceeded(
                        $ip,
                        $this->investorService->getByEmail($validated['email']),
                        BlockedIp::BLOCKED_IP_REASON_LOGIN
                    )
                ) {
                    $wrongLoginBlockDays = (int) \SettingFacade::getSettingValue(
                        Setting::WRONG_LOGIN_BLOCK_DAYS_KEY
                    );

                    return redirect()->route('profile')->with(
                        'fail',
                        __(
                            'auth.throttleLogin',
                            ['time' => Carbon::now()->addDayS($wrongLoginBlockDays)]
                        )
                    );
                }

                $this->loginAttemptService->create($validated['email'], $device, $ip);
            }


            return redirect()->back()
                ->withInput($request->only('email', 'password'))
                ->with('fail', __('auth.failed'));

        } catch (Throwable $e) {
            return view('errors.generic');
        }
    }

    public function logout()
    {
        try {
            Auth::guard('investor')->logout();

            return redirect()->route('homepage');
        } catch (Throwable $e) {
            return view('errors.generic');
        }
    }
}
