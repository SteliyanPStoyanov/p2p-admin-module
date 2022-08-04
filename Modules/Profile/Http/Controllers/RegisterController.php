<?php

namespace Modules\Profile\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Entities\Investor;
use Modules\Common\Services\BlockedIpService;
use Modules\Common\Services\InvestorLoginLogService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\PDFCreatorService;
use Modules\Common\Services\PortfolioService;
use Modules\Common\Services\RegistrationAttemptService;
use Modules\Common\Services\WalletService;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Profile\Http\Requests\RegisterRequest;
use Modules\Profile\Http\Requests\RegisterStepTwoRequest;
use Throwable;

class RegisterController extends BaseController
{
    protected InvestorLoginLogService $investorLoginLogService;
    protected RegistrationAttemptService $registrationAttemptService;
    protected BlockedIpService $blockedIpService;
    protected InvestorService $investorService;
    protected EmailService $emailService;
    protected PortfolioService $portfolioService;
    protected WalletService $walletService;
    protected PDFCreatorService $pdfCreatorService;

    /**
     * RegisterController constructor.
     *
     * @param RegistrationAttemptService $registrationAttemptService
     * @param BlockedIpService $blockedIpService
     * @param InvestorService $investorService
     * @param EmailService $emailService
     * @param PortfolioService $portfolioService
     * @param WalletService $walletService
     * @param InvestorLoginLogService $investorLoginLogService
     * @param PDFCreatorService $pdfCreatorService
     *
     * @throws \ReflectionException
     */
    public function __construct(
        RegistrationAttemptService $registrationAttemptService,
        BlockedIpService $blockedIpService,
        InvestorService $investorService,
        EmailService $emailService,
        PortfolioService $portfolioService,
        WalletService $walletService,
        InvestorLoginLogService $investorLoginLogService,
        PDFCreatorService $pdfCreatorService
    ) {
        $this->middleware('guest:investor');
        $this->registrationAttemptService = $registrationAttemptService;
        $this->blockedIpService = $blockedIpService;
        $this->investorService = $investorService;
        $this->emailService = $emailService;
        $this->portfolioService = $portfolioService;
        $this->walletService = $walletService;
        $this->investorLoginLogService = $investorLoginLogService;
        $this->pdfCreatorService = $pdfCreatorService;

        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function registerFrom()
    {
        try {
            return view('profile::register.index');
        } catch (Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        try {
            $device = $request->header('User-Agent');
            $ip = $request->ip();


            // check if IP is blocked
            $blockedTill = $this->blockedIpService->blockedTill($ip, BlockedIp::BLOCKED_IP_REASON_REGISTER);
            if ($blockedTill) {
                return redirect()->route('profile.register')->with(
                    'fail',
                    __(
                        'auth.throttleRegister',
                        [
                            'time' => $blockedTill->blocked_till,
                        ]
                    )
                );
            }

            // if we do many wrong attempts - block
            if ($this->registrationAttemptService->attemptCount($ip, BlockedIp::BLOCKED_IP_REASON_REGISTER) == true) {
                return redirect()->route('profile.register')->with(
                    'fail',
                    __(
                        'auth.throttleRegister',
                        [
                            'time' => Carbon::now()->addHours(config('profile.blocked_time'))
                        ]
                    )
                );
            }


            // mandatory formatting
            $validated['email'] = strtolower(trim($validated['email']));


            // register login attempt
            $this->registrationAttemptService->create(
                $validated['email'],
                $device,
                $ip
            );


            // if email exists
            if ($this->investorService->emailExist($validated['email']) == true) {
                $investor = $this->investorService->getByEmail($validated['email']);

                // if user need to continue registration
                if ($investor->status == Investor::INVESTOR_STATUS_UNREGISTERED) {
                    $request->session()->put('investorId', $investor->investor_id);
                    return redirect()->route('profile.createAccount')->with(
                        'fail',
                        __('common.UserEmailExistsProfileNotComplete')
                    );
                }


                // if try to use used email
                return redirect()->route('profile.login')->with(
                    'fail',
                    __('common.UserEmailExists')
                );
            }


            // create and investor
            $investor = $this->investorService->create(
                $validated['email'],
                $validated['referral_id'] ?? null
            );


            // if everything is OK, and we created investor -> go to next registration step
            if (!empty($investor->investor_id)) {
                $request->session()->put('investorId', $investor->investor_id);
                return redirect()->route('profile.createAccount');
            }


            return redirect()->back('profile.register')->with(
                'fail',
                __('common.UserRegisterFail')
            );
        } catch (Throwable $e) {
            return view('errors.generic');
        }
    }

    public function createAccountForm()
    {
        try {
            return view('profile::register.create');
        } catch (Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param RegisterStepTwoRequest $request
     * @return RedirectResponse
     */
    public function createAccount(RegisterStepTwoRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        try {
            $investorId = $request->session()->get('investorId');

            // prevent double click
            $investor = $this->investorService->getById($investorId);
            if (
                !empty($investor->status)
                && $investor->status == Investor::INVESTOR_STATUS_REGISTERED
            ) {
                sleep(3);
                return redirect()->route('profile.login');
            }


            $investor = $this->investorService->stepUpdate($investorId, $validated);

            if ($investor) {
                $this->emailService->sendWelcomeEmail($investor);

                $this->portfolioService->addNewInvestorPortfolio($investor->investor_id);

                $this->walletService->addNewInvestorWallet($investor->investor_id);

                $this->investorService->refreshInvestorAgreements(
                    [
                        'add-funds' => true,
                        'withdrawal-made' => true,
                        'new-device' => true,
                    ],
                    $investor
                );

                $this->pdfCreatorService->generateUserAgreement($investor);

                if ($validated['type'] == Investor::TYPE_COMPANY) {
                    $this->investorService->addCompany($investorId, $validated);
                }

                if ($this->investorService->emailExist($investor->email) == true) {
                    if (Auth::guard('investor')->attempt(
                        ['email' => $investor->email, 'password' => $validated['password'], 'active' => 1]
                    )) {
                        $device = $request->header('User-Agent');

                        $investorId = $this->getInvestor()->investor_id;

                        $this->investorLoginLogService->create($device, $investorId, false);

                        return redirect()->route('profile.dashboard.overview');
                    }
                }
            }
        } catch (Throwable $e) {
            return redirect()->route('profile')->with('fail', __('common.UserRegisterFail'));
        }

        return redirect()->route('profile')->with('fail', __('common.UserRegisterFail'));
    }

    /**
     * @throws Throwable
     */
    public function investorTypeHtml(string $type)
    {
        if ($type == Investor::TYPE_INDIVIDUAL) {
            return view('profile::register.individual')->render();
        }

        return view('profile::register.company')->render();
    }
}
