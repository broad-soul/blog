<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SubscribeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subs;
    public function __construct($subscriber)
    {
        $this->subs = $subscriber;
    }

    public function build()
    {
        return $this->from('opentest00@mail.ru')->view('emails.verify');
    }
}
