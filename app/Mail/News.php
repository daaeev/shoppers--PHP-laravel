<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\News as NewsModel;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class News extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public NewsModel $news)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.news');
    }
}
