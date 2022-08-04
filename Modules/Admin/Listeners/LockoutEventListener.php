<?php

namespace Modules\Admin\Listeners;

use Illuminate\Auth\Events\Lockout;
use Modules\Admin\Entities\Administrator;

class LockoutEventListener
{
    /**
     * Handle the event.
     *
     * @param Lockout $event
     *
     * @return void
     */
    public function handle(Lockout $event)
    {
        if ($event->request->has('username')) {
            $administrator = Administrator::where('username', $event->request->get('username'))->first();
            if (!empty($administrator)) {
                $administrator->disable();
            }
        }
    }
}
