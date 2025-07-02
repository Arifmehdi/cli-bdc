<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageSendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public function __construct($content)
    {
        $this->data = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('We have a message from Bestdreamcar')->view('email.message-mail');
    }


}
