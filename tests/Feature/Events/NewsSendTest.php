<?php

namespace Tests\Feature\Events;

use App\Events\NewsSend;
use App\Models\News;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NewsSendTest extends TestCase
{
    public function testEventDispatched()
    {
        Event::fake(NewsSend::class);

        $news = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->getMock();

        NewsSend::dispatch($news);

        Event::assertDispatched(NewsSend::class);
    }
}
