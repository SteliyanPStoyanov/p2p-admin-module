<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\Document;
use Modules\Core\Repositories\BaseRepository;

class DocumentRepository extends BaseRepository
{

    /**
     * @param array $data
     *
     * @return Document
     */
    public function create(array $data)
    {
        $document = new Document();
        $document->fill($data);
        $document->save();

        return $document;
    }

}
