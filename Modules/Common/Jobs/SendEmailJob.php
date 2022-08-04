<?php

namespace Modules\Common\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Modules\Common\Emails\MailSend;
use Modules\Communication\Entities\Email;
use Throwable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Email $email;

    public $timeout = 50;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function handle()
    {
        $send = Mail::send(new MailSend($this->email));
        if (Mail::failures()) {
            return false;
        }

        $this->email->send_at = Carbon::now();
        $this->email->save();

        return true;
    }

    /**
     * @param Throwable $exception
     */
    public function failed(Throwable $exception)
    {
    }
}
