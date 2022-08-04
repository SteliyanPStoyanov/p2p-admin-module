<?php

namespace Modules\Admin\Services;

use App;
use Carbon\Carbon;
use DB;
use Modules\Admin\Repositories\UserAgreementRepository;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Entities\File;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\VariablesService;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\StorageService;
use Storage;
use Throwable;

class UserAgreementService extends BaseService
{
    protected const START_HTML_TEMPLATE = '<body>';
    protected const END_HTML_TEMPLATE = '</body>';
    protected const PAGE_BREAK = '<div class="page-break"></div>';

    protected UserAgreementRepository $userAgreementRepository;
    protected StorageService $storageService;
    protected VariablesService $variableService;

    public function __construct(
        UserAgreementRepository $userAgreementRepository,
        StorageService $storageService,
        VariablesService $variableService
    ) {
        $this->userAgreementRepository = $userAgreementRepository;
        $this->storageService = $storageService;
        $this->variableService = $variableService;

        parent::__construct();
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function getTemplateById(int $id)
    {
        $template = $this->userAgreementRepository->getById($id);
        if (!$template) {
            throw new NotFoundException('common.TemplateNotFound');
        }

        return $template;
    }

    /**
     * @param array $data
     *
     * @return ContractTemplate
     *
     * @throws ProblemException
     */
    public function create(array $data)
    {
        try {
            $data['variables'] = json_encode(
                $data['type'] === ContractTemplate::TYPE_INVESTOR ? ContractTemplate::USER_AGREEMENT_VARS : ContractTemplate::ASSIGNMENT_AGREEMENT_VARS
            );

            $template = $this->userAgreementRepository->create($data);
            if ($data['type'] === ContractTemplate::TYPE_LOAN) {
                $this->generateAssignmentAgreementTemplate();
            }
        } catch (Throwable $e) {
            throw new ProblemException(__('common.UserAgreementCreationFailure'));
        }

        return $template;
    }

    /**
     * @return string
     */
    protected function getStartAssignmentAgreement(string $text)
    {
        return mb_strcut(
            $text,
            0,
            strpos($text, self::START_HTML_TEMPLATE) + strlen(self::START_HTML_TEMPLATE)
        );
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function getEndAssignmentAgreement(string $text)
    {
        return mb_strcut(
            $text,
            strpos($text, self::END_HTML_TEMPLATE),
            strlen($text)
        );
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function getContentAssignmentAgreement(string $text)
    {
        $start = strpos($text, self::START_HTML_TEMPLATE) + strlen(self::START_HTML_TEMPLATE);
        $end = strlen($text) - strpos($text, self::END_HTML_TEMPLATE);

        return mb_substr(
            $text,
            $start,
            -$end
        );
    }

    /**
     * @return false|string
     *
     * @throws ProblemException
     */
    public function generateAssignmentAgreementTemplate()
    {
        $assignmentAgreements = $this->getAllContractTemplatesByType(ContractTemplate::TYPE_LOAN);

        if (empty($assignmentAgreements)) {
            return false;
        }
        $start = null;
        $text = null;
        $end = null;
        foreach ($assignmentAgreements as $key => $assignmentAgreement) {
            $assignmentAgreementText = $assignmentAgreement->text;
            if ($text == null) {
                $start = $this->getStartAssignmentAgreement($assignmentAgreementText);
            }

            $text .= $this->getContentAssignmentAgreement($assignmentAgreementText);
            $text .= self::PAGE_BREAK;

            if ($end == null) {
                $end = $this->getEndAssignmentAgreement($assignmentAgreementText);
            }
        }

        $text = ($start . $text . $end);
        $vars = [
            'ContractTemplate' => [
                'version' => '[Version of agreement]',
                'created_at' => '[Date of creation]'
            ],
            'Loan' => [
                'loan_id' => '[Loan ID]',
                'interest_rate_percent' => '[Interest rate percent]',
                'final_payment_date' => '[Loan final payment date]',
            ],
            'Investor' => [
                'investor_id' => '[Investor ID]',
            ],
            'Transaction' => [
                'created_at' => '[Transaction date]',
                'transaction_id' => '[Transaction ID]',
            ],
            'Originator' => [
                'name' => '[Originator name]',
                'country' => [
                    'name' => '[Originator country]',
                ],
                'pin' => '[Originator ID]'
            ],
            'Investment' => [
                'amount' => '[Investment amount]',
            ],
            'Buyback' => '[Yes/No]',
            'Afranga' => [
                'pin' => '[Afranga ID]',
            ],
            'LoanContract' => [
                'created_at' => '[Assignment agreement date]'
            ],
            'Agreement' => [
                'language' => '[Agreement language]',
            ],
        ];

        $content = $this->variableService->replaceVariables($text, $vars);
        $storageService = App::make(StorageService::class);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($content);

        $filePath = StorageService::ASSIGNMENT_AGREEMENT_TEMPLATE_DIR;
        $storageService->storeFile($filePath, $pdf->output());

        return $filePath;
    }


    /**
     * @param $id
     * @param array $data
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function update($id, array $data)
    {
        $template = $this->getTemplateById($id);

        try {
            $this->userAgreementRepository->edit($template, $data);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.UserAgreementEditFailure'));
        }
    }

    /**
     * @param $id
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function delete($id)
    {
        $template = $this->getTemplateById($id);
        if ($template->isActive()) {
            throw new ProblemException(__('common.UserAgreementDeleteForbidden'));
        }

        try {
            $this->userAgreementRepository->delete($template);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.UserAgreementDeleteFailure'));
        }
    }

    /**
     * @param $id
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function enable($id)
    {
        $contractTemplate = $this->getTemplateById($id);
        if ($contractTemplate->isActive()) {
            throw new ProblemException(__('common.UserAgreementEnableForbidden'));
        }

        try {
            DB::beginTransaction();

            $this->userAgreementRepository->disableCurrentContract($contractTemplate->type);
            $this->userAgreementRepository->enable($contractTemplate);

            if ($contractTemplate->type == ContractTemplate::TYPE_INVESTOR) {
                $this->updateUserAgreementTemplate();
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new ProblemException(__('common.UserAgreementEnableFail'));
        }
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

        return $this->userAgreementRepository->getAll($length, $whereConditions);
    }

    protected function getWhereConditions(array $data, array $names = ['name'], $prefix = '')
    {
        $where = [];
        if (!empty($data['createdAt'])) {
            if (!empty($data['createdAt']['from'])) {
                $where[] = [
                    'contract_template.created_at',
                    '>=',
                    dbDate($data['createdAt']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['createdAt']['to'])) {
                $where[] = [
                    'contract_template.created_at',
                    '<=',
                    dbDate($data['createdAt']['to'], '23:59:59'),
                ];
            }

            unset($data['createdAt']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param string $type
     *
     * @return ContractTemplate
     */
    public function getCurrentContractTemplate(string $type)
    {
        return $this->userAgreementRepository->getCurrentUserAgreement($type);
    }

    public function createInvestorContract(
        Investor $investor,
        File $file,
        ContractTemplate $userAgreementTemplate,
        array $vars
    ) {
        return $this->userAgreementRepository->createInvestorContract(
            [
                'investor_id' => $investor->investor_id,
                'contract_template_id' => $userAgreementTemplate->contract_template_id,
                'file_id' => $file->file_id,
                'data' => json_encode($vars),
            ]
        );
    }

    public function createLoanContract(
        Loan $loan,
        Investor $investor,
        File $file,
        ContractTemplate $contract,
        Investment $investment,
        array $vars
    ) {
        return $this->userAgreementRepository->createLoanContract(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
                'contract_template_id' => $contract->contract_template_id,
                'investment_id' => $investment->investment_id,
                'file_id' => $file->file_id,
                'data' => json_encode($vars),
            ]
        );
    }

    /**
     * @throws ProblemException
     */
    public function updateUserAgreementTemplate()
    {
        // Delete current agreement
        Storage::delete(StorageService::CURRENT_USER_AGREEMENT);

        $currentUserAgreement = $this->getCurrentContractTemplate(ContractTemplate::TYPE_INVESTOR);

        $content = $currentUserAgreement->text;
        $vars = [
            'Investor' => [
                'investor_id' => '',
            ],
            'Current' => [
                'date' => Carbon::now()->toDateString(),
            ],
            'ContractTemplate' => $currentUserAgreement->toArray(),
        ];
        $content = $this->variableService->replaceVariables($content, $vars);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($content);

        $this->storageService->storeFile(StorageService::CURRENT_USER_AGREEMENT, $pdf->output());
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function getAllContractTemplatesByType(string $type)
    {
        return $this->userAgreementRepository->getAllContractTemplates($type);
    }
}
