<?php

namespace Modules\Common\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Communication\Entities\Email;

class MailSend extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;

    /**
     * SendEmail constructor.
     *
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->html($this->email->text)
            ->to($this->email->sender_to)
            ->subject($this->email->title);
    }
}
