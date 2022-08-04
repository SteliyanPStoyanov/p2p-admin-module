<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\File;
use Modules\Core\Repositories\BaseRepository;

class FileRepository extends BaseRepository
{
    protected File $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @param array $data
     *
     * @return File
     */
    public function create(array $data)
    {
        $file = new File();
        $file->fill($data);
        $file->save();

        return $file;
    }

    /**
     * @param int $fileId
     *
     * @return mixed
     */
    public function getById(int $fileId)
    {
        return File::where(
            'file_id',
            '=',
            $fileId
        )->first();
    }

    /**
     * @param array $fileNames
     *
     * @return mixed
     */
    public function getImportedFilesWithNewLoans(array $fileNames)
    {
        return File::whereIn('file_name', $fileNames)->get();
    }

    /**
     * @param File $file
     *
     * @throws \Exception
     */
    public function delete(File $file)
    {
        $file->delete();
    }
}
