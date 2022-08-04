<?php

namespace Modules\Common\Services;

use Modules\Common\Entities\DocumentType;
use Modules\Common\Repositories\DocumentTypeRepository;
use Modules\Core\Services\BaseService;

class DocumentTypeService extends BaseService
{
    private DocumentTypeRepository $documentTypeRepository;

    /**
     * @param DocumentTypeRepository $documentTypeRepository
     */
    public function __construct(
        DocumentTypeRepository $documentTypeRepository

    ) {
        $this->documentTypeRepository = $documentTypeRepository;

        parent::__construct();
    }


    public function getAll()
    {
        return $this->documentTypeRepository->getAll();
    }

    public function getClientDocumentTypes(): array
    {
        $data = [];
        $data[] = (object) [
            'document_type_id' => DocumentType::DOCUMENT_TYPE_ID_IDCARD,
            'name' => DocumentType::DOCUMENT_TYPE_NAME_IDCARD,
        ];
        $data[] = (object) [
            'document_type_id' => DocumentType::DOCUMENT_TYPE_ID_PASSPORT,
            'name' => DocumentType::DOCUMENT_TYPE_NAME_PASSPORT,
        ];

        return $data;
    }
}
