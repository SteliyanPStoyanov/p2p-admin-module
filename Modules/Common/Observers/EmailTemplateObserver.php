<?php

namespace Modules\Common\Observers;

use Modules\Communication\Entities\EmailTemplate;
use Modules\Communication\Services\EmailTemplateService;
use Modules\Core\Services\CacheService;

class EmailTemplateObserver
{
    /**
     * @param EmailTemplate $emailTemplate
     *
     * @return void
     */
    public function updating(EmailTemplate $emailTemplate)
    {
        $emailTemplateService = \App::make(EmailTemplateService::class);

        (new CacheService())->remove($emailTemplateService->setTemplateKey($emailTemplate->email_template_id));
    }
}
