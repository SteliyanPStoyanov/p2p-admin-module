<?php

namespace Modules\Common\Services;

use Modules\Common\Entities\File;
use Modules\Common\Repositories\FileRepository;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;

class FileService extends BaseService
{
    private FileRepository $fileRepository;

    /**
     * @param FileRepository $fileRepository
     */
    public function __construct(
        FileRepository $fileRepository
    ) {
        $this->fileRepository = $fileRepository;

        parent::__construct();
    }

    /**
     * @param array $file
     */
    public function create(array $file)
    {
        return $this->fileRepository->create($file);
    }

    /**
     * @param int $id
     *
     * @return File
     */
    public function getById(int $id)
    {
        $file = $this->fileRepository->getById($id);
        if (empty($file)) {
            abort(404);
        }

        return $file;
    }
}
