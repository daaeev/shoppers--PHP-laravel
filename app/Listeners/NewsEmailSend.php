<?php

namespace App\Listeners;

use App\Events\NewsSend;
use App\Mail\News;
use App\Services\Interfaces\SubscribeRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class NewsEmailSend
{
    public function __construct(protected SubscribeRepositoryInterface $subscribeRepository)
    {

    }

    /**
     * Handle the event.
     *
     * @param  NewsSend $event
     * @return void
     */
    public function handle(NewsSend $event)
    {
        $mail = app(News::class, ['news' => $event->news]);
        $emails = $this->subscribeRepository->getEmails();

        Mail::to($emails)->queue($mail);
    }
}
