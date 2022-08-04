<?php

namespace Modules\Common\Observers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\Investor;
use Modules\Common\Repositories\AffiliateInvestorRepository;
use Modules\Common\Services\InvestorService;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Services\CacheService;

class InvestorObserver
{
    /**
     * @param Investor $investor
     * @return false
     */
    public function created(Investor $investor)
    {
        $affiliateInvestorRepository = new AffiliateInvestorRepository();
        $cookie = (new CacheService)->get('affiliate');

        try {
            if (!empty($cookie->client_id)) {
                if ($affiliateInvestorRepository->isExists($cookie->client_id) === true) {
                    $affiliateInvestorRepository->update($investor->investor_id, $cookie->client_id);
                }
            }
        } catch (Exception $e) {
            Log::channel('affiliate')->error(
                'Failed to save change log. ' . $e->getMessage()
            );
            return false;
        }
    }

    public function updating(Investor $investor)
    {
        if (
            $investor->isDirty('first_name')
            && $investor->status === Investor::INVESTOR_STATUS_REGISTERED
        ) {
            if (!empty($investor->referral_id)) {
                $investorService = \App::make(InvestorService::class);
                $emailTemplateId = EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_email']['id'];
                $parentInvestor = $investorService->getById($investor->referral_id);
                $service = \App::make(EmailService::class);

                $sendEmail = $service->sendEmail(
                    $parentInvestor,
                    $emailTemplateId,
                    $parentInvestor->email,
                    Carbon::now(),
                    ['referralFirstName' => $investor->first_name]
                );

                if ($sendEmail == false) {
                    Log::channel('email_service')->error(
                        'Failed to send email to parent investor id: ' . $investor->referral_id
                    );
                }
            }
            (new CacheService)->remove('affiliate');
            Mail::raw(
                $investor->first_name . ' ' . $investor->last_name . ' ' . $investor->email . ' registered.',
                function ($message) {
                    $env = strtoupper(env('APP_ENV'));
                    $message->from(config('mail.from.address'));
                    $message->to(config('mail.log_monitor.receivers'));
                    $message->subject('(' . $env . ') New investor registered');
                }
            );
        }


        if ($investor->isDirty('password')) {
            $msg = 'Password changed #' . $investor->investor_id . ' (' . $investor->email . '), '
                . 'old: ' . $investor->getOriginal('password') . ', '
                . 'new: ' . $investor->password;

            Log::channel('specific')->info($msg);

            // $send = Mail::raw(
            //     $msg,
            //     function ($message) {
            //         $message->from('sd@stikcredit.bg', 'PASSWORD ALERT');
            //         $message->to('sd@stikcredit.bg');
            //         $message->subject('!!! PASSWORD ALERT !!!');
            //     }
            // );
        }
    }
}
