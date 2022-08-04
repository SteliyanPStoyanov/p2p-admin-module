<?php

namespace Modules\Common\Listeners;

use Illuminate\Support\Carbon;
use Modules\Common\Events\RestorePassword;
use Modules\Common\Services\RestoreHashService;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailService;

class RestorePasswordListener
{
    protected EmailService $emailService;
    protected RestoreHashService $restoreHashService;

    /**
     * RestorePasswordListener constructor.
     *
     * @param EmailService $emailService
     * @param RestoreHashService $restoreHashService
     */
    public function __construct(
        EmailService $emailService,
        RestoreHashService $restoreHashService
    ) {
        $this->emailService = $emailService;
        $this->restoreHashService = $restoreHashService;
    }

    /**
     * @param RestorePassword $restorePassword
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function handle(RestorePassword $restorePassword)
    {
        $email = $restorePassword->investor->email;
        $investorId = $restorePassword->investor->investor_id;

        $hash = $this->restoreHashService->create($investorId);

        $additionalData = [
            'restorePasswordUrl' => route('profile.restorePassword', $hash->hash),
        ];

        $this->emailService->sendEmail(
            $investorId,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['forgot_password']['id'],
            $email,
            Carbon::now(),
            $additionalData
        );
    }
}

