<?php

namespace Modules\Common\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Common\Entities\Investor;

class RestorePassword
{
     use Dispatchable, InteractsWithSockets, SerializesModels;

     public Investor $investor;

    /**
     * RestorePassword constructor.
     *
     * @param Investor $investor
     */
    public function __construct(Investor $investor)
    {
        $this->investor = $investor;
    }
}
