<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\BlogPageCrudRequest;
use Modules\Admin\Http\Requests\BlogPageSearchRequest;
use Modules\Admin\Services\BlogPageService;
use Modules\Core\Controllers\BaseController;

class BlogPageController extends BaseController
{
    protected string $indexRoute = 'admin.blog-page.list';

    protected $blogPageService;

    public function __construct(BlogPageService $blogPageService)
    {
        $this->blogPageService = $blogPageService;

        parent::__construct();
    }

    /**
     * @param BlogPageSearchRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(BlogPageSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        return view(
            'admin::blog-page.list',
            [
                'blogPages' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]

        );
    }

    /**
     * @param BlogPageSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(BlogPageSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::blog-page.list-table',
            [
                'blogPages' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        return $this->blogPageService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @param BlogPageSearchRequest $request
     */
    protected function checkForRequestParams(
        BlogPageSearchRequest $request
    ) {
        if ($request->exists(
            ['blog_page_id', 'title', 'context']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param int $blogPageId
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function edit(int $blogPageId)
    {
        if (empty($blogPageId)) {
            return redirect()->route($this->indexRoute)
                ->with('fail', 'Wrong BLog Page ID');
        }

        $blogPage = $this->blogPageService->getBlogPageById($blogPageId);

        if (empty($blogPage)) {
            return redirect()->route($this->indexRoute)
                ->with('fail', 'Not existing blog page');
        }

        return view(
            'admin::blog-page.edit',
            compact('blogPage')
        );
    }

    /**
     * @param BlogPageCrudRequest $request
     * @param $blogPageId
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function update(BlogPageCrudRequest $request, $blogPageId)
    {
        $blogPage = $this->blogPageService->update($blogPageId, $request->validated());

        if ($request->hasFile('images')) {
            $this->uploadImage($request, $blogPage);
        }

        return redirect()
            ->route('admin.blog-page.list')
            ->with('success', __('common.BlogPageUpdatedSuccessfully'));
    }

    private function uploadImage($request, $blogPage)
    {
        if ($request->hasFile('images')) {
            $this->blogPageService->addImage(
                $blogPage,
                $request->file('images')
            );
        }
    }

    /**
     * @param int $blogPageId
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function enable(int $blogPageId)
    {
        $this->blogPageService->enable($blogPageId);

        return redirect()
            ->route($this->indexRoute)
            ->with(
                'success',
                __('common::blogPageEnabledSuccessfully')
            );
    }

    /**
     * @param int $blogPageId
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function disable(int $blogPageId)
    {
        $this->blogPageService->disable($blogPageId);

        return redirect()
            ->route($this->indexRoute)
            ->with(
                'success',
                __('common::blogPageDisabledSuccessfully')
            );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view(
            'admin::blog-page.create',
        );
    }

    /**
     * @param BlogPageCrudRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function store(BlogPageCrudRequest $request)
    {
        $blogPage = $this->blogPageService->createBlogPage($request->validated());

        if ($request->hasFile('images')) {
            $this->blogPageService->addImage(
                $blogPage,
                $request->file('images')
            );
        }

        return redirect()
            ->route($this->indexRoute)
            ->with(
                'success',
                __('common::blogPageCreatedSuccessfully')
            );
    }

    /**
     * @param int $blogPageId
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function delete(int $blogPageId)
    {
        $blogPage = $this->blogPageService->getBlogPageById($blogPageId);

        if ($blogPage->files()->exists()) {
            foreach ($blogPage->files as $file) {
                $this->blogPageService->deleteImage(
                    intval($blogPage->blog_page_id),
                    intval($file->file_id),
                    $file->file_name
                );
            }

            $this->blogPageService->delete($blogPageId);
        } else {
            $this->blogPageService->delete($blogPageId);
        }

        return redirect()
            ->route($this->indexRoute)
            ->with(
                'success',
                __('common.BlogPageDeletedSuccessfully')
            );
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function ajaxDeleteImage(Request $request): array
    {
        try {
            if ($request->ajax() == true) {
                return [
                    'success' => true,
                    'data' => [
                        'blogPageImage' => $this->blogPageService->deleteImage(
                            intval($request->blog_page_id),
                            intval($request->file_id),
                            $request->file_name
                        ),
                        'blogPage' => $this->blogPageService->getBlogPageById((intval($request->blog_page_id))),
                    ]
                ];
            }
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'data' => [
                    'blogPageImage' => null,
                ]
            ];
        }
    }
}
