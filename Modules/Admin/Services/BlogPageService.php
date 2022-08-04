<?php

namespace Modules\Admin\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Entities\BlogPage;
use Modules\Admin\Repositories\BlogPageRepository;
use Modules\Common\Entities\DocumentType;
use Modules\Common\Entities\File;
use Modules\Common\Entities\FileStorage;
use Modules\Common\Entities\FileType;
use Modules\Common\Repositories\FileRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\StorageService;

class BlogPageService extends BaseService
{

    protected BlogPageRepository $blogPageRepository;
    protected StorageService $storageService;
    protected FileRepository $fileRepository;

    public function __construct(
        BlogPageRepository $blogPageRepository,
        StorageService $storageService,
        FileRepository $fileRepository
    ) {
        $this->blogPageRepository = $blogPageRepository;
        $this->storageService = $storageService;
        $this->fileRepository = $fileRepository;

        parent::__construct();
    }

    /**
     * @param int $length
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditions(int $length, array $data)
    {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditions(
            $data,
            [
                'administrator.first_name',
                'administrator.middle_name',
                'administrator.last_name'
            ],
            'administrator'
        );

        return $this->blogPageRepository->getAll($length, $whereConditions);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];

        if (!empty($data['createdAt']['from'])) {
            $where[] = [
                'blog_page.created_at',
                '>=',
                dbDate($data['createdAt']['from'], '00:00:00'),
            ];
        }

        if (!empty($data['createdAt']['to'])) {
            $where[] = [
                'blog_page.created_at',
                '<=',
                dbDate($data['createdAt']['to'], '23:59:59'),
            ];
        }

        unset($data['createdAt']);

        if (!empty($data['tags'])) {
            $where['tags'] = $data['tags'];
            unset($data['tags']);
        }

        if (isset($data['active'])) {
            $where[] = [
                'blog_page.active',
                '=',
                $data['active'],
            ];
            unset($data['active']);
        }


        if (isset($data['deleted'])) {
            $where[] = [
                'blog_page.deleted',
                '=',
                $data['deleted'],
            ];
            unset($data['deleted']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param $data
     * @param array $files
     *
     * @return BlogPage
     * @throws ProblemException
     */
    public function createBlogPage($data)
    {
        try {
            $tagArr['tag'] = explode(',', $data['tags']);
            $data['tags'] = json_encode($tagArr);
            $data['date'] = dbDate($data['date']);
            return $this->blogPageRepository->create($data);
        } catch (\Exception $exception) {
            throw new ProblemException(
                __('common.BlogPageCreationFailed'),
                $exception->getMessage()
            );
        }
    }

    public function addImage(BlogPage $blogPage, array $files)
    {
        $i = 0;
        foreach ($files as $file) {
            $i++;
            $fileStore = $this->storageService->uploadBlogPageImage($blogPage->blog_page_id, $file, $i);
            $fileData['hash'] = Hash::make($file->getClientOriginalName());
            $fileData['file_path'] = $fileStore[0];
            $fileData['file_size'] = $file->getSize();
            $fileData['file_type'] = $file->getClientMimeType();
            $fileData['file_type_id'] = FileType::IMAGE_BLOG_ID;
            $fileData['file_name'] = $fileStore[1];
            $savedFile = $this->fileRepository->create($fileData);

            $blogPage->files()->attach(['blog_page_id' => $blogPage->blog_page_id], ['file_id' => $savedFile->file_id]);
        }

        return $savedFile;
    }


    public function update(int $blogPageId, $data)
    {
        $blogPage = $this->blogPageRepository->getById($blogPageId);
        $tagArr['tag'] = explode(',', $data['tags']);
        $data['tags'] = json_encode($tagArr);
        $data['date'] = dbDate($data['date']);

        try {
            $blogPage = $this->blogPageRepository->update($blogPage, $data);
            return $blogPage;
        } catch (\Exception $exception) {
            throw new ProblemException(
                __('common.BlogEditionFailed'),
                $exception->getMessage()
            );
        }
    }

    /**
     * @param int $blogPostId
     *
     * @return bool
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function enable(int $blogPostId)
    {
        $blogPage = $this->getBlogPageById($blogPostId);

        try {
            $this->blogPageRepository->enable($blogPage);
        } catch (\Exception  $exception) {
            throw new ProblemException(
                __('common.blogPageEnabledFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }

    /**
     * @param int $blogPostId
     *
     * @return bool
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function disable(int $blogPostId)
    {
        $blogPage = $this->getBlogPageById($blogPostId);

        try {
            $this->blogPageRepository->disable($blogPage);
        } catch (\Exception  $exception) {
            throw new ProblemException(
                __('common.blogPageDisabledFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }


    /**
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundException
     */
    public function getBlogPageById(int $id)
    {
        $blogPage = $this->blogPageRepository->getById($id);

        if (!$blogPage) {
            throw new NotFoundException(__('common::blogPageNotFound'));
        }

        return $blogPage;
    }

    /**
     * @param int $blogPageId
     *
     * @return bool
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function delete(int $blogPageId)
    {
        $blogPage = $this->getBlogPageById($blogPageId);

        try {
            $this->blogPageRepository->delete($blogPage);
        } catch (\Exception $exception) {
            throw new ProblemException(
                __('common.blogPageDeletionFailed'),
                $exception->getMessage()
            );
        }

        return true;
    }

    public function deleteImage(int $blogPageId, int $blogPageImageId, string $fileName)
    {

        try {
            $blogPage = $this->getBlogPageById($blogPageId);
            $this->storageService->deleteBlogPageImage($fileName);

            $blogPage->files()->detach(['file_id' => $blogPageImageId]);

        } catch (NotFoundException $e) {
            throw new ProblemException(
                __('common.blogPageNotFound'),
                $e->getMessage()
            );
        }
    }

    public function getBlogPages()
    {
        return $this->blogPageRepository->getBlogPages();
    }

    public function getArchives()
    {
        return $this->blogPageRepository->getArchives();
    }

    public function getByArchive(string $month)
    {
        return $this->blogPageRepository->getByArchive($month);
    }

}
