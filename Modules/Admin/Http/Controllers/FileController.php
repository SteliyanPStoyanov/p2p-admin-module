<?php

namespace Modules\Admin\Http\Controllers;

use Modules\Common\Services\FileService;
use Modules\Core\Controllers\BaseController;

class FileController extends BaseController
{
    protected FileService $fileService;

    /**
     * FileController constructor.
     *
     * @param FileService $fileService
     *
     * @throws \ReflectionException
     */
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;

        parent::__construct();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getFileById(int $id)
    {
        $file = $this->fileService->getById($id);
        $filePath = storage_path() . '/' . $file->file_path . $file->file_name;

        switch (\File::mimeType($filePath)) {
            case 'image/png':
            case 'image/jpeg':
            case 'image/gif':
            case 'image/bmp':
            case 'application/pdf':
                return response()->file($filePath);
            default:
                return response()->download($filePath, $file->file_name);
        }
    }
}
