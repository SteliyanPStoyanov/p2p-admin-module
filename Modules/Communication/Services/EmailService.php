<?php

namespace Modules\Communication\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\ChangeLog;
use Modules\Common\Entities\Investor;
use Modules\Common\Jobs\SendEmailJob;
use Modules\Common\Repositories\InvestorRepository;
use Modules\Common\Services\LoanService;
use Modules\Communication\Entities\Email;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Repositories\EmailRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Modules\Common\Services\VariablesService;
use Throwable;

class EmailService extends BaseService
{
    const QUEUE_NAME = 'emails';

    private EmailRepository $emailRepository;
    private EmailTemplateService $emailTemplateService;
    private LoanService $loanService;
    private VariablesService $variablesService;
    private InvestorRepository $investorRepository;

    public function __construct(
        EmailRepository $emailRepository,
        EmailTemplateService $emailTemplateService,
        LoanService $loanService,
        VariablesService $variablesService,
        InvestorRepository $investorRepository
    ) {
        $this->emailRepository = $emailRepository;
        $this->emailTemplateService = $emailTemplateService;
        $this->loanService = $loanService;
        $this->variablesService = $variablesService;
        $this->investorRepository = $investorRepository;

        parent::__construct();
    }

    /**
     * @param Investor $investor
     * @param array $data
     *
     * @return bool
     */
    public function sendReferralLink(
        Investor $investor,
        array $data
    ) {
        $additionalData = [
            'referral_link' => route('profile.hash', $investor->referral_hash)
        ];

        return $this->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['referral_link']['id'],
            $data['email'],
            Carbon::now(),
            $additionalData
        );
    }

    /**
     * @param Investor $investor
     *
     * @return bool
     */
    public function sendWelcomeEmail(Investor $investor)
    {
        return $this->sendEmail(
            $investor,
            EmailTemplate::TEMPLATE_SEEDER_ARRAY['welcome_template']['id'],
            $investor->email,
            Carbon::now()
        );
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function getById(int $id)
    {
        $email = $this->emailRepository->getById($id);
        if (!$email) {
            throw new NotFoundException(__('common.smsTemplateNotFound'));
        }

        return $email;
    }

    /**
     * @param int $length
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditions(
        int $length,
        array $data
    ) {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditions($data);

        return $this->emailRepository->getAll($length, $whereConditions);
    }


    /**
     * @param $investor
     * @param int $emailTemplateId
     * @param string $email
     * @param Carbon|null $queueDelay
     * @param array $additionalData
     *
     * @return bool
     */
    public function sendEmail(
        $investor, // int OR Investor
        int $emailTemplateId,
        string $email,
        Carbon $queueDelay = null,
        array $additionalData = []
    ): bool {
        try {
            return $this->send($investor, $emailTemplateId, $email, $queueDelay, $additionalData);
        } catch (Throwable | ProblemException $e) {
            Log::channel('email_service')->error(
                'Failed to send email. ' . $e->getMessage()
            );
            return false;
        }
    }

    /**
     * @param int|Investor $investor
     * @param int $emailTemplateId
     * @param string $email
     * @param Carbon|null $queueDelay
     * @param array $additionalData
     *
     * @return bool
     * @throws NotFoundException
     * @throws ProblemException
     */
    protected function send(
        $investor, // int OR Investor
        int $emailTemplateId,
        string $email,
        Carbon $queueDelay = null,
        array $additionalData = []
    ) {
        if (is_numeric($investor)) {
            $investor = $this->investorRepository->getById($investor);
        }

        if (!$investor) {
            throw new NotFoundException(__('common.investorNotFound'));
        }

        $emailTemplate = $this->emailTemplateService->getTemplateById($emailTemplateId);
        if (!$emailTemplate) {
            throw new NotFoundException(__('common.emailTemplateNotFound'));
        }

        $mergeVariables = array_merge(
            config('communication.emailDefaultVariables'),
            ['Investor' => $investor->toArray()],
            $additionalData
        );

        try {
            $text = $this->variablesService->replaceVariables(
                $emailTemplate->text,
                $mergeVariables
            );
        } catch (Throwable $e) {
            throw new ProblemException(__('common.htmlVariablesReplaceProblem'));
        }

        $queueDelay = $queueDelay ?: Carbon::now();
        $data = [
            'email_template_id' => $emailTemplate->email_template_id,
            'investor_id' => $investor->investor_id,
            'title' => $emailTemplate->title,
            'body' => $emailTemplate->body,
            'text' => $text,
            'sender_from' => config('mail.from.address'),
            'sender_to' => $email,
            'queue' => self::QUEUE_NAME,
            'queued_at' => $queueDelay,
        ];

        try {
            $emailSend = $this->emailRepository->create($data);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.emailCreationFailed') . '|' . $e->getMessage());
        }

        SendEmailJob::dispatch($emailSend)
            ->onQueue(self::QUEUE_NAME)
            ->delay($queueDelay->addSeconds(30));

        return true;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function checkReferralEmail(string $email): bool
    {
        if ($this->investorRepository->isExists($email) == true) {
            return true;
        }

        return ChangeLog::where(
                [
                    'old_value' => $email,
                    'key' => 'investor.email',
                ]
            )->count() > 0;
    }

}
