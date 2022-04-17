<?php

namespace Tests\Feature\Controllers;

use App\Events\NewsSend;
use App\Models\News;
use App\Models\User;
use App\Services\Interfaces\NewsRepositoryInterface;
use App\Services\Repositories\NewsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NewsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testCreateSuccess()
    {
        $data = [
            'title' => 'Title',
            'content' => 'Content',
        ];

        $model_mock = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($data);

        $this->instance(
            News::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.news.create'), $data)
            ->assertRedirect(route('admin.news'));

        $response->assertSessionHas('status_success');
    }

    public function testCreateModelSaveFailed()
    {
        $data = [
            'title' => 'Title',
            'content' => 'Content',
        ];

        $model_mock = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($data);

        $this->instance(
            News::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.news.create'), $data)
            ->assertRedirect(route('admin.news'));

        $response->assertSessionHas('status_failed');
    }

    public function testNewsSendSuccess()
    {
        $model = News::factory()->createOne();

        $data = ['id' => $model->id];

        Event::fake(NewsSend::class);

        $model_mock = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setAttribute'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setAttribute')
            ->with('sent', true);

        $rep_mock = $this->getMockBuilder(NewsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($model->id)
            ->willReturn($model_mock);

        $this->instance(
            NewsRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.news.send'), $data)
            ->assertRedirect(route('admin.news'));

        $response->assertSessionHas('status_success');

        Event::assertDispatched(NewsSend::class);
    }

    public function testNewsSendModelSaveFailed()
    {
        $model = News::factory()->createOne();

        $data = ['id' => $model->id];

        Event::fake(NewsSend::class);

        $model_mock = $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setAttribute'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setAttribute')
            ->with('sent', true);

        $rep_mock = $this->getMockBuilder(NewsRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($model->id)
            ->willReturn($model_mock);

        $this->instance(
            NewsRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.news.send'), $data)
            ->assertRedirect(route('admin.news'));

        $response->assertSessionHas('status_warning');

        Event::assertDispatched(NewsSend::class);
    }
}
