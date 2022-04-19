<?php

namespace Tests\Feature\Listeners;

use App\Events\NewsSend;
use App\Listeners\NewsEmailSend;
use App\Models\News;
use App\Mail\News as MailNews;
use App\Models\Subscribe;
use App\Services\Interfaces\SubscribeRepositoryInterface;
use App\Services\Repositories\SubscribeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsEmailSendTest extends TestCase
{
    public function testListening()
    {
        Event::fake(NewsSend::class);

        $news = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->getMock();

        NewsSend::dispatch($news);

        Event::assertListening(NewsSend::class,NewsEmailSend::class);
    }

    public function testListenerFunctionalIfEmailExist()
    {
        Mail::fake();

        $news = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sub1 = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sub2 = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sub1->email = 'test1@test.ua';
        $sub2->email = 'test2@test.ua';

        $rep_mock = $this->getMockBuilder(SubscribeRepository::class)
            ->onlyMethods(['getEmails'])
            ->disableOriginalConstructor()
            ->getMock();

        $collection = Collection::make([$sub1, $sub2]);

        $rep_mock->expects($this->once())
            ->method('getEmails')
            ->willReturn($collection);

        $this->instance(
            SubscribeRepositoryInterface::class,
            $rep_mock
        );

        NewsSend::dispatch($news);

        Mail::assertQueued(MailNews::class, function ($mail) use ($collection) {
            return $mail->hasTo($collection);
        });
    }

    public function testListenerFunctionalIfEmailsNotExists()
    {
        Mail::fake();

        $news = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->getMock();

        $rep_mock = $this->getMockBuilder(SubscribeRepository::class)
            ->onlyMethods(['getEmails'])
            ->disableOriginalConstructor()
            ->getMock();

        $collection = Collection::make([]);

        $rep_mock->expects($this->once())
            ->method('getEmails')
            ->willReturn($collection);

        $this->instance(
            SubscribeRepositoryInterface::class,
            $rep_mock
        );

        NewsSend::dispatch($news);

        Mail::assertNothingQueued();
    }
}
