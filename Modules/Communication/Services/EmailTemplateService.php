<?php

namespace Modules\Communication\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Repositories\EmailTemplateRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\CacheService;
use ReflectionClass;
use Throwable;

class EmailTemplateService extends BaseService
{
    private EmailTemplateRepository $emailTemplateRepository;
    private CacheService $cacheService;

    public function __construct(
        EmailTemplateRepository $userAgreementRepository,
        CacheService $cacheService
    ) {
        $this->emailTemplateRepository = $userAgreementRepository;
        $this->cacheService = $cacheService;

        parent::__construct();
    }

    /**
     * @return mixed
     * @throws NotFoundException
     */
    public function getAllEmailTemplates()
    {
        $emailTemplates = $this->emailTemplateRepository->getAll();

        if (!$emailTemplates) {
            throw new NotFoundException(__('common.emailTemplateNotFound'));
        }

        return $emailTemplates;
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
        $emailTemplates = $this->cacheService->get($this->setTemplateKey($id));

        if ($emailTemplates === null) {
            $emailTemplates = $this->emailTemplateRepository->getById($id);
            $this->cacheService->set(
                $this->setTemplateKey($id),
                $emailTemplates
            );

            if (!$emailTemplates) {
                throw new NotFoundException(__('common.emailTemplateNotFound'));
            }

            return $emailTemplates;
        }


        return EmailTemplate::hydrate([$emailTemplates])[0];
    }

    /**
     * @param array $data
     *
     * @return \Modules\Communication\Entities\EmailTemplate
     * @throws ProblemException
     */
    public function create(array $data)
    {
        $data['key'] = Str::of($data['title'])->slug('_');
        try {
            $emailTemplates = $this->emailTemplateRepository->create($data);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.emailTemplateCreationFailed'));
        }
        return $emailTemplates;
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
        $emailTemplates = $this->getTemplateById($id);

        $data['key'] = Str::of($data['title'])->slug('_');

        try {
            $this->emailTemplateRepository->edit($emailTemplates, $data);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.emailTemplateUpdateFailed'));
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
        $emailTemplates = $this->getTemplateById($id);

        try {
            $this->emailTemplateRepository->delete($emailTemplates);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.emailTemplateDeletionFailed'));
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
        $emailTemplates = $this->getTemplateById($id);
        if ($emailTemplates->isActive()) {
            throw new ProblemException(__('common.emailTemplateEnableForbidden'));
        }

        try {
            $this->emailTemplateRepository->enable($emailTemplates);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.emailTemplateEnableFailed'));
        }
    }

    /**
     * @param $id
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function disable($id)
    {
        $emailTemplates = $this->getTemplateById($id);
        if (!$emailTemplates->isActive()) {
            throw new ProblemException(__('common.emailTemplateDisableForbidden'));
        }

        try {
            $this->emailTemplateRepository->disable($emailTemplates);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.emailTemplateDisableFailed'));
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

        return $this->emailTemplateRepository->getAll($length, $whereConditions);
    }

    /**
     * @param int $id
     * @return string
     */
    public function setTemplateKey(int $id): string
    {
        $class = (new ReflectionClass($this))->getShortName();

        return sprintf(
            "%s.%s.%s",
            'cache',
            strtolower(str_replace('Service', '', $class)),
            $id
        );
    }
}
