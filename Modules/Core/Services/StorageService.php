<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use \Exception;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\BlogPage;
use Modules\Common\Entities\File;
use Modules\Common\Entities\FileStorage;
use Modules\Common\Imports\NewLoansImport;
use Modules\Common\Imports\UnlistedLoanImport;
use Modules\Common\Repositories\FileRepository;
use Modules\Core\Exceptions\ExportWrongFormatException;
use Modules\Core\Exceptions\ProblemException;
use Intervention\Image\ImageManagerStatic as Image;
use Throwable;

/**
 * Class StorageService
 * Responsible for:
 * - storing files
 * - downloading files
 * - generating exports
 * - importing files
 *
 * @package Modules\Core\Services
 */
class StorageService
{
    public $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    const AVATAR_ALIAS = 'ava_';
    const AVATAR_EXTENSION = 'jpg';
    const AVATAR_PATH = 'avatars/';

    const BLOG_PAGE_ALIAS = 'blog-page-images';
    const BLOG_PAGE_EXTENSION = 'jpg';
    const BLOG_PAGE_IMAGE_PATH = 'blog-page/';

    const PERSONAL_DOC_PATH = 'personal-doc/';
    const COMPANY_DOC_PATH = 'company-doc/';
    const DOC_EXTENSION = 'png';
    const DEFAULT_AVATAR_PATH = '/images/avatars/default.jpg';
    const DEFAULT_STORAGE_PATH = '/storage/';
    const PATH_TO_EXPORTS = '/exports/';
    const PATH_TO_SETTLEMENT = '/settlement/';
    const PATH_TO_PDF_TEMPLATE = 'common::pdf.view';
    const PATH_TO_WALLET = '/wallet/';
    const PATH_TO_LOAN_OUTSTANDING = '/loans_outstanding/';
    const PATH_TO_STRATEGIES_BALANCE = '/strategies_balance/';
    const PATH_TO_BONUS_TRACKING = '/bonus_tracking/';

    const IMPORT_UNLISTED_LOANS_FILE_NAME_TEMPLATE = 'unlisted_loans_%s.%s';
    const UNLISTED_LOANS_DIR = 'import/unlisted/';
    const IMPORTED_UNLISTED_LOANS_DIR = '/imported/unlisted/';
    const IMPORTED_UNLISTED_LOANS_NAME = 'unlisted_loans';

    const IMPORT_LOANS_FILE_NAME_TEMPLATE = 'loans_%s.csv';
    const IMPORT_LOANS_DIR = 'import/loans/';
    const IMPORTED_LOANS_DIR = '/imported/loans/';
    const IMPORTED_LOANS_NAME_SITE = 'loans_site';
    const IMPORTED_LOANS_NAME_OFFICE = 'loans_office';
    const IMPORT_LOANS_DATE_FORMAT = 'Y-m-d H:i:s';

    const IMPORTED_PAYMENTS_DIR = 'import/imported-payments/';
    const IMPORTED_PAYMENTS_NAME = 'imported_payments';

    const ASSIGNMENT_AGREEMENT_DIR = 'contracts/loan/contract_%s_date_%s.pdf';
    const ASSIGNMENT_AGREEMENT_TEMPLATE_DIR = 'contracts/assignment_agreement_templates.pdf';
    const USER_AGREEMENT_DIR = 'contracts/investor/%s/contract_investor_id_%s.pdf';
    const CURRENT_USER_AGREEMENT = 'contracts/investor/user_agreement.pdf';

    protected const FILE_FORMATS = [
        'xls',
        'xlsx',
        'csv',
        'pdf'
    ];

    private const IMPORT_FORMATS = [
        'xls',
        'xlsx',
        'csv',
    ];


    /**
     * @param Administrator $admin
     * @param UploadedFile $file
     *
     * @return string
     */
    public function uploadAvatar(
        Administrator $admin,
        UploadedFile $file
    ): string {
        $fileNameNew = self::getAdminAvatarPath($admin->getId());
        Storage::disk('public')->put($fileNameNew, file_get_contents($file));
        return $fileNameNew;
    }

    public function uploadBlogPageImage(int $blogPageId, UploadedFile $file, int $increment)
    {
        $path = self::BLOG_PAGE_ALIAS . '/';

        if (!self::hasFile($path)) {
            Storage::disk('public')->makeDirectory($path, 0777, true, true);
        }
        switch ($file->getMimeType()) {
            case 'image/png':
            case 'image/jpeg':
            case 'image/gif':
            case 'image/bmp':
                $newFileName = 'blogPage_' . $blogPageId . '_' . time() . '_' . $increment . '.' . self::DOC_EXTENSION;
                Image::make($file)
                    ->resize(
                        1024,
                        null,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        }
                    )
                    ->save(storage_path() . '/' . $path . '/' . $newFileName);
                break;
            default:
                $newFileName = 'blogPage_' . $blogPageId . '_' . time() . '_' . $increment . '.' . $file->extension();

                $this->storeFile(($path . $newFileName), file_get_contents($file));
        }

        return [$path, $newFileName];
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function deleteBlogPageImage(string $fileName): bool
    {
        return Storage::disk('public')->delete('blog-page-images' . '/' . $fileName);
    }

    /**
     * @param int $investorId
     * @param UploadedFile $file
     * @param int $increment
     * @return string[]
     * @throws ProblemException
     */
    public function uploadPersonalDoc(int $investorId, UploadedFile $file, int $increment)
    {
        $path = self::PERSONAL_DOC_PATH . $investorId . '/';

        if (!self::hasFile($path)) {
            Storage::disk('public')->makeDirectory($path, 0777, true, true);
        }

        switch ($file->getMimeType()) {
            case 'image/png':
            case 'image/jpeg':
            case 'image/gif':
            case 'image/bmp':
                $newFileName = 'personalDoc_' . $investorId . '_' . time(
                    ) . '_' . $increment . '.' . self::DOC_EXTENSION;

                Image::make($file)
                    ->resize(
                        1024,
                        null,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        }
                    )
                    ->save(storage_path() . '/' . $path . $newFileName);
                break;
            default:
                $newFileName = 'personalDoc_' . $investorId . '_' . time() . '_' . $increment . '.' . $file->extension(
                    );

                $this->storeFile(($path . $newFileName), file_get_contents($file));
        }

        return [$path, $newFileName];
    }

    /**
     * @param int $investorId
     * @param UploadedFile $file
     * @param int $increment
     * @return string[]
     * @throws ProblemException
     */
    public function uploadCompanyDoc(int $investorId, $file, int $increment)
    {
        $path = self::COMPANY_DOC_PATH . $investorId . '/';

        if (!self::hasFile($path)) {
            Storage::disk('public')->makeDirectory($path, 0777, true, true);
        }

        switch ($file->getMimeType()) {
            case 'image/png':
            case 'image/jpeg':
            case 'image/gif':
            case 'image/bmp':
                $newFileName = 'companyDoc_' . $investorId . '_' . time(
                    ) . '_' . $increment . '.' . self::DOC_EXTENSION;

                Image::make($file)
                    ->resize(
                        1024,
                        null,
                        function ($constraint) {
                            $constraint->aspectRatio();
                        }
                    )
                    ->save(storage_path() . '/' . $path . $newFileName);
                break;
            default:
                $newFileName = 'companyDoc_' . $investorId . '_' . time() . '_' . $increment . '.' . $file->extension(
                    );

                $this->storeFile(($path . $newFileName), file_get_contents($file));
        }

        return [$path, $newFileName];
    }

    /**
     * [getAdminAvatarPath description]
     *
     * @param int $adminId
     *
     * @return string
     */
    public static function getAdminAvatarPath(int $adminId): string
    {
        return sprintf(
            '%s%s%d.%s',
            self::AVATAR_PATH,
            self::AVATAR_ALIAS,
            $adminId,
            self::AVATAR_EXTENSION
        );
    }

    public static function getBlogPageImagePath(int $blogPageId): string
    {
        return sprintf(
            '%s%s%d.%s',
            self::BLOG_PAGE_IMAGE_PATH,
            self::BLOG_PAGE_ALIAS,
            $blogPageId,
            self::BLOG_PAGE_EXTENSION
        );
    }


    /**
     * @param string $path
     *
     * @return bool
     */
    public static function hasFile(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    public static function renameFile(
        string $filepath,
        string $oldName,
        string $newName
    ): void {
        Storage::move($filepath . $oldName, $filepath . $newName);
    }

    /**
     * @param string $documentTemplateKey
     * @param int $loanId
     * @param int $clientId
     *
     * @return string
     */
    public function makeDir(
        string $documentTemplateKey,
        int $loanId,
        int $clientId
    ): string {
        $path = config('docs.filePath') . $documentTemplateKey . '/';
        if (
            !empty(Auth::user()->administrator_id)
            && (
                Auth::user()->administrator_id
                == Administrator::DEFAULT_UNIT_TEST_USER_ID
            )
        ) {
            $path = config('docs.filePath')
                . config('docs.filePathTesting') . '/'
                . $documentTemplateKey . '/';
        }

        if (!self::hasFile($path)) {
            Storage::disk('public')->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * @param string $fileName
     * @param array $config
     * @param string $format
     * @param string $dirToSave
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function download(
        string $fileName,
        array $config,
        string $format = 'xlsx',
        string $dirToSave = self::PATH_TO_EXPORTS
    ) {
        $filePath = $this->generate(
            $fileName,
            $config,
            $format,
            $dirToSave
        );

        if (!$filePath) {
            throw new Exception('Empty download path');
        }

        return $this->downloadFromStorage($filePath);
    }

    /**
     * Important! Works only with relative path
     *
     * @param string $filePath
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadFromStorage(string $filePath)
    {
        return Storage::disk('public')->download($filePath);
    }

    /**
     * @param string $fileName
     * @param array $config
     * @param string $format
     * @param string $dirToSave
     *
     * @return string - relative path to file
     *
     * @throws \Exception
     * @throws \ExportWrongFormatException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function generate(
        string $fileName,
        array $config,
        string $format = 'xlsx',
        string $dirToSave = self::PATH_TO_EXPORTS
    ): string {
        if (!in_array($format, self::FILE_FORMATS)) {
            throw new ExportWrongFormatException($format . ' is not allowed.');
        }

        $downloadPath = $dirToSave . $fileName . '.' . $format;
        switch ($format) {
            case 'csv':
            case 'xls':
            case 'xlsx':
                if (empty($config['collectionClass'])) {
                    throw new Exception('Empty collection for ' . $format . ' export');
                }

                Excel::store(
                    $config['collectionClass'],
                    $downloadPath,
                    'public'
                );
                break;
            case 'pdf':
                if (empty($config['content'])) {
                    throw new Exception('No content for pdf export');
                }

                PDF::setOptions(
                    [
                        'logOutputFile' => storage_path() . '/logs/log.htm',
                        'tempDir' => storage_path() . '/logs/',
                    ]
                )->loadView(
                    self::PATH_TO_PDF_TEMPLATE,
                    $config
                )->save(storage_path() . '/' . $downloadPath);
        }

        return $downloadPath;
    }

    /**
     * Get file which should be imported
     *
     *
     * @param string|null $fileName
     *
     * @return array
     */
    public function getImportLoanFilesByFileName(string $fileName = null): array
    {
        $allFiles = $this->getImportLoanFiles();
        if (empty($allFiles)) {
            return [];
        }

        if (empty($fileName)) {
            // TODO
            $filtered = [];
            foreach ($allFiles as $fileName) {
                if ($fileName == 'import/loans/.gitignore') {
                    continue;
                }
                $filtered[] = $fileName;
            }

            return $filtered;
        }

        $files = preg_grep('/(\/' . $fileName . ')$/', $allFiles);
        if (empty($files)) {
            return [];
        }

        return $files;
    }

    /**
     * @param null|string $filePath
     *
     * @return array
     */
    public function getImportUnlistedLoanFiles(?string $filePath = null): array
    {
        $files = [];

        $allFiles = $this->getAllFilesInDir(self::UNLISTED_LOANS_DIR);
        if (empty($allFiles)) {
            return [];
        }

        if ($filePath != null) {
            $files = preg_grep('/(\/' . $filePath . ')+$/', $allFiles);

            return $files;
        }

        $template = sprintf(
            self::IMPORT_UNLISTED_LOANS_FILE_NAME_TEMPLATE,
            '([0-9]{4}\-[0-9]{2}\-[0-9]{2}\-[0-9]{6})',
            '(csv|xlsx|xls)'
        );
        $files = preg_grep('/(\/' . $template . ')+$/', $allFiles);
        if (empty($files)) {
            return [];
        }

        return $files;
    }

    public function getImportLoanFiles()
    {
        return $this->getAllFilesInDir(self::IMPORT_LOANS_DIR);
    }

    public function getAllFilesInDir(string $dir)
    {
        return Storage::files($dir);
    }

    public function readFile(string $pathToFile): string
    {
        return Storage::get($pathToFile);
    }

    /**
     * Parse imported csv file with 2 columns: credit_id, percent
     * Return an array with data, where key=credit_id, value=percent
     *
     * @param string $pathToFile
     *
     * @return array
     */
    public function getParsedData(string $pathToFile): array
    {
        $ext = pathinfo($pathToFile, PATHINFO_EXTENSION);
        if ($ext == 'xls' || $ext == 'xlsx') {
            $file = Excel::toArray(new NewLoansImport(), $pathToFile)[0];
            $lines = array_map(
                function ($val) {
                    if (is_null($val['credit_id']) || is_null($val['interest_rate'])) {
                        return ',';
                    }
                    return $val['credit_id'] . ',' . $val['interest_rate'];
                },
                $file
            );
        } else {
            $lines = str_getcsv($this->readFile($pathToFile), "\n");
        }


        $result = [];
        array_walk(
            $lines,
            function ($line) use (&$result, &$counter) {
                $line = preg_replace("/\r\n|\r|\n/", '', $line);
                if (preg_match("/^([0-9]+)\,([0-9\.]+)$/", $line, $m)) {
                    $result[$m[1]] = $m[2];
                }
            }
        );

        return $result;
    }

    public function loansFileFromOffice(string $fileName): bool
    {
        return preg_match('/(' . self::IMPORTED_LOANS_NAME_OFFICE . ')/', $fileName);
    }

    public function loansFileFromSite(string $fileName): bool
    {
        return preg_match('/(' . self::IMPORTED_LOANS_NAME_SITE . ')/', $fileName);
    }

    /**
     * Parse imported csv file with 1 columns: lender_id
     * Return an array with data, where value=lender_id
     *
     * @param string $pathToFile
     *
     * @return array
     */
    public function getParsedUnlistedData(string $pathToFile): array
    {
        $ext = pathinfo($pathToFile, PATHINFO_EXTENSION);
        if ($ext == 'xls' || $ext == 'xlsx') {
            $lines = array_filter(array_column(Excel::toArray(new UnlistedLoanImport(), $pathToFile)[0], 'credit_id'));
        } else {
            $lines = str_getcsv($this->readFile($pathToFile), "\n");
        }

        $result = [];
        array_walk(
            $lines,
            function ($line) use (&$result) {
                $line = preg_replace("/\r\n|\r|\n/", '', $line);
                if (preg_match("/^([0-9]+)$/", $line, $m)) {
                    $result[] = $m[1];
                }
            }
        );

        return array_unique($result);
    }

    public function moveImportedFileWithLoans(string $filePath, string $newDir)
    {
        return Storage::move(
            $filePath,
            $newDir . $this->getFileNameFromPath($filePath)
        );
    }

    public function moveImportedFileWithUnlistedLoans(string $filePath)
    {
        File::where('file_name', '/' . $filePath)->update(
            [
                'active' => 0,
                'deleted' => 1,
                'updated_at' => Carbon::now(),
                'updated_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ]
        );
        return Storage::move(
            $filePath,
            self::IMPORTED_UNLISTED_LOANS_DIR . $this->getFileNameFromPath($filePath)
        );
    }

    public function getFileNameFromPath(string $filePath)
    {
        return substr(strrchr($filePath, '/'), 1);
    }

    /**
     * @param UploadedFile $file
     * @param int $fileTypeId
     * @param string $filePath
     * @param string $newFileName
     * @param bool $returnFileName
     *
     * @return bool|string
     *
     * @throws ProblemException
     */
    public function import(
        UploadedFile $file,
        int $fileTypeId,
        string $filePath,
        string $newFileName,
        bool $returnFileName = false
    ) {
        try {
            $now = Carbon::now();
            $ext = $file->getClientOriginalExtension();
            $this->isCorrectFormat($ext, self::IMPORT_FORMATS);

            $originNameWithExt = $file->getClientOriginalName();
            $originNameWithExt = $changeNameWithExt = $newFileName;
            $originName = pathinfo($originNameWithExt, PATHINFO_FILENAME);
            $newFileName = $filePath . $originName . '_' . Str::slug(
                    Carbon::createFromFormat(
                        self::IMPORT_LOANS_DATE_FORMAT,
                        $now
                    )
                ) . '.' . $ext;
            $content = file_get_contents($file);
            $isSaved = $this->storeFile($newFileName, $content);

            if ($isSaved) {
                // Create file log history
                $newFileData = [
                    'file_storage_id' => FileStorage::FILE_STORAGE_HARD_DISC_ONE_ID,
                    'file_type_id' => $fileTypeId,
                    'hash' => $newFileName,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'file_type' => $ext,
                    'file_name' => $newFileName,
                ];

                $this->fileRepository->create($newFileData);
            }
        } catch (Throwable $e) {
            throw new ProblemException(
                __('common.FileProblemImporting'),
                $e->getMessage()
            );
        }

        return ($returnFileName ? $newFileName : true);
    }

    /**
     * @param string $ext
     * @param array $formats
     *
     * @return bool
     * @throws ProblemException
     */
    private function isCorrectFormat(
        string $ext,
        array $formats
    ): bool {
        if (!in_array($ext, $formats)) {
            throw new ProblemException(
                __('common.InvalidImportFormat') .
                implode(', ', $formats)
            );
        }

        return true;
    }

    /**
     * @param string $fileName
     * @param string $content
     *
     * @return bool
     * @throws ProblemException
     */
    public function storeFile(
        string $fileName,
        string $content
    ) {
        $isSaved = Storage::disk('local')
            ->put($fileName, $content);

        if (!$isSaved) {
            throw new ProblemException(
                __('common.FileIsNotSaved')
            );
        }

        return $isSaved;
    }
}
