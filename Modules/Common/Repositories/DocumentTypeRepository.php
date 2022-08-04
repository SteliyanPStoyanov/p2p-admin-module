<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\DocumentType;
use Modules\Core\Repositories\BaseRepository;

class DocumentTypeRepository extends BaseRepository
{
    public function getAll()
    {
        return DocumentType::where(
            'active',
            '=',
            '1'
        )->get();
    }
}
