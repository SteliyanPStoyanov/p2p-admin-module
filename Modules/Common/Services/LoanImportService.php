<?php

namespace Modules\Common\Services;

use Modules\Common\Imports\NewLoansImport;
use Modules\Common\Imports\UnlistedLoanImport;
use Modules\Core\Services\BaseService;
use Illuminate\Support\Facades\Storage;
use Modules\Common\Repositories\FileRepository;
use Modules\Common\Repositories\LoanRepository;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\StorageService;
use Illuminate\Http\UploadedFile;
use Exception;
use Throwable;

class LoanImportService extends BaseService
{
    private LoanRepository $loanRepository;
    private StorageService $storageService;
    private FileRepository $fileRepository;

    public function __construct(
        LoanRepository $loanRepository,
        StorageService $storageService,
        FileRepository $fileRepository
    ) {
        $this->loanRepository = $loanRepository;
        $this->storageService = $storageService;
        $this->fileRepository = $fileRepository;

        parent::__construct();
    }

    /**
     * @param int $loanId
     *
     * @return mixed
     */
    public function getById(int $loanId)
    {
        return $this->loanRepository->getById($loanId);
    }

    /**
     * @param UploadedFile $file
     * @param int $fileTypeId
     * @param string $filePath
     * @param string $newFileName
     *
     * @return bool
     * @throws ProblemException
     */
    public function importFile(
        UploadedFile $file,
        int $fileTypeId,
        string $filePath,
        string $newFileName
    ) {
        try {
            return $importedFile = $this->storageService->import($file, $fileTypeId, $filePath, $newFileName);
        } catch (Throwable $e) {
            throw new ProblemException(
                __('common.FileProblemImporting'),
                $e->getMessage()
            );
        }
    }

    /**
     * @param int $documentId
     * @param string $newDir
     *
     * @return bool
     *
     * @throws ProblemException
     */
    public function deleteDocumentWithNewLoans(int $documentId, string $newDir)
    {
        $file = $this->fileRepository->getById($documentId);

        try {
            $this->fileRepository->delete($file);

            return $this->storageService->moveImportedFileWithLoans($file->file_name, $newDir);
        } catch (Throwable $e) {
            throw new ProblemException(
                __('common.FileProblemImporting'),
                $e->getMessage()
            );
        }
    }

    /**
     * @param int $fileId
     *
     * @return mixed
     * @throws ProblemException
     */
    public function downloadFile(int $fileId)
    {
        $file = $this->fileRepository->getById($fileId);

        try {
            return Storage::disk('public')->download($file->file_name);
        } catch (Throwable $e) {
            throw new ProblemException(
                __('common.FileProblemDownloading'),
                $e->getMessage()
            );
        }
    }

    /**
     * @param array $fileNames
     *
     * @return mixed
     * @throws ProblemException
     */
    public function getFilesByNames(array $fileNames)
    {
        try {
            return $this->fileRepository->getImportedFilesWithNewLoans($fileNames);
        } catch (Throwable $e) {
            throw new ProblemException(
                __('common.FileProblem'),
                $e->getMessage()
            );
        }
    }

    /**
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function isValidRebuyingLoanFile(UploadedFile $file): bool
    {
        $data = \Excel::toArray(new UnlistedLoanImport(), $file);
        if (empty($data)) {
            return false;
        }

        // foreach ($data as $sheet) {
        //     foreach ($sheet as $rows) {

        //         if (
        //             !array_key_exists('credit_id', $rows)
        //             && !array_key_exists('contract_id', $rows)
        //         ) {
        //             return false;
        //         }

        //         if (
        //             is_null($rows['credit_id'])
        //             || is_null($rows['contract_id'])
        //         ) {
        //             continue;
        //         }

        //         if (
        //             (
        //                 !is_numeric($rows['credit_id'])
        //                 && !is_numeric($rows['contract_id'])
        //             )
        //             || count($rows) != 1
        //         ) {
        //             return false;
        //         }
        //     }
        // }

        return true;
    }

    /**
     * @param UploadedFile $file
     *
     * @return bool
     */
    public function isValidNewLoansFile(UploadedFile $file): bool
    {
        $data = \Excel::toArray(new NewLoansImport(), $file);
        if (empty($data)) {
            return false;
        }

        // foreach ($data as $sheet) {
        //     foreach ($sheet as $rows) {
        //         if (
        //             !array_key_exists('interest_rate', $rows)
        //             || (
        //                 !array_key_exists('credit_id', $rows)
        //                 !array_key_exists('contract_id', $rows)
        //             )
        //         ) {
        //             return false;
        //         }

        //         if (
        //             (
        //                 is_null($rows['contract_id'])
        //                 || is_null($rows['contract_id'])
        //             ) && is_null($rows['interest_rate'])
        //         ) {
        //             continue;
        //         }

        //         if (
        //             !is_numeric($rows['contract_id'])
        //             || !is_numeric($rows['interest_rate'])
        //         ) {
        //             return false;
        //         }
        //     }
        // }

        return true;
    }
}
