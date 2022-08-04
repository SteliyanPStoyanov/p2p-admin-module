<?php

namespace Modules\Common\Services;

use App;
use Carbon\Carbon;
use Hash;
use Illuminate\Support\Str;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\Afranga;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Entities\FileStorage;
use Modules\Common\Entities\FileType;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\LoanContract;
use Modules\Common\Entities\Transaction;
use Modules\Core\Services\StorageService;
use Storage;

class PDFCreatorService
{
    protected VariablesService $variableService;
    protected UserAgreementService $userAgreementService;
    protected FileService $fileService;

    public function __construct(
        VariablesService $variableService,
        UserAgreementService $userAgreementService,
        FileService $fileService
    ) {
        $this->variableService = $variableService;
        $this->userAgreementService = $userAgreementService;
        $this->fileService = $fileService;
    }

    public function generateAssignmentAgreement(
        Investment $investment,
        Transaction $transaction
    ): bool
    {
        $assignmentAgreement = $this->userAgreementService->getCurrentContractTemplate(ContractTemplate::TYPE_LOAN);
        if (empty($assignmentAgreement)) {
            return false;
        }

        $loan = $investment->loan;
        $investor = $investment->investor();

        $vars = [
            'ContractTemplate' => $assignmentAgreement->toArray(),
            'Loan' => $loan->toArray(),
            'Investor' => $investor->toArray(),
            'Transaction' => $transaction->toArray(),
            'Originator' => $loan->originator->toArray(),
            'Investment' => $investment->toArray(),
            'Buyback' => $loan->buyback ? 'Yes' : 'No',
            'Afranga' => [
                'pin' => Afranga::PIN,
            ],
            'LoanContract' => [
                'created_at' => Carbon::now()->toDateString()
            ],
            'Agreement' => [
                'language' => ContractTemplate::AGREEMENT_LANGUAGE,
            ],
        ];
        $vars['Transaction']['created_at'] = Carbon::parse($vars['Transaction']['created_at'])->format('dmY');
        $vars['Originator']['name'] = Str::upper($vars['Originator']['name']);

        $filePath = $this->generateAgreement(
            $assignmentAgreement->text,
            $vars,
            sprintf(
                StorageService::ASSIGNMENT_AGREEMENT_DIR,
                $investment->loan_id,
                Carbon::now()->format('Y-m-d_H:i:s')
            )
        );

        $file = $this->fileService->create(
            [
                'file_storage_id' => FileStorage::FILE_STORAGE_HARD_DISC_ONE_ID,
                'file_type_id' => FileType::INVESTOR_CONTRACT_ID,
                'file_path' => Storage::path($filePath),
                'file_size' => Storage::size($filePath),
                'file_type' => Storage::mimeType($filePath),
                'file_name' => $filePath,
                'hash' => Hash::make($filePath),
            ]
        );

        $this->userAgreementService->createLoanContract(
            $loan,
            $investor,
            $file,
            $assignmentAgreement,
            $investment,
            $vars
        );

        return true;
    }

    /**
     * @param $content
     * @param array $vars
     * @param string $filePath
     *
     * @return string
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function generateAgreement($content, array $vars, string $filePath)
    {
        $content = $this->variableService->replaceVariables($content, $vars);
        $storageService = App::make(StorageService::class);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($content);

        $storageService->storeFile($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * @param Investor $investor
     *
     * @return bool|void
     *
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function generateUserAgreement(Investor $investor)
    {
        $userAgreementTemplate = $this->userAgreementService->getCurrentContractTemplate(
            ContractTemplate::TYPE_INVESTOR
        );
        if (empty($userAgreementTemplate)) {
            return false;
        }

        $vars = [
            'Investor' => $investor->toArray(),
            'Current' => [
                'date' => Carbon::now()->toDateString(),
            ],
            'ContractTemplate' => $userAgreementTemplate->toArray(),
        ];

        $filePath = $this->generateAgreement(
            $userAgreementTemplate->text,
            $vars,
            sprintf(
                StorageService::USER_AGREEMENT_DIR,
                $investor->investor_id,
                Carbon::now()->format('Y-m-d_H:i:s')
            )
        );

        $file = $this->fileService->create(
            [
                'file_storage_id' => FileStorage::FILE_STORAGE_HARD_DISC_ONE_ID,
                'file_type_id' => FileType::INVESTOR_CONTRACT_ID,
                'file_path' => Storage::path($filePath),
                'file_size' => Storage::size($filePath),
                'file_type' => Storage::mimeType($filePath),
                'file_name' => $filePath,
                'hash' => Hash::make($filePath),
            ]
        );

        $this->userAgreementService->createInvestorContract($investor, $file, $userAgreementTemplate, $vars);
    }

    public function hasContractForInvestment(int $investmentId): bool
    {
        $installmentsCount = LoanContract::where([
            'investment_id' => $investmentId,
        ])->count();

        return ($installmentsCount > 0);
    }
}
