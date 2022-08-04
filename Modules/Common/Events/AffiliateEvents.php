<?php

namespace Modules\Common\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AffiliateEvents
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $fullUrl;

    /**
    * AffiliateEvents constructor.
    * @param $fullUrl
    */
    public function __construct($fullUrl)
    {
        $this->fullUrl = $fullUrl;
    }
}
