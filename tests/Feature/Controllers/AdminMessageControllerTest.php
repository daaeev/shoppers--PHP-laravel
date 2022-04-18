<?php

namespace Tests\Feature\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Services\Interfaces\MessageRepositoryInterface;
use App\Services\Repositories\MessageRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testSetAnsweredStatusSuccess()
    {
        $mess = Message::factory()->createOne();

        $data = ['id' => $mess->id];

        $model_mock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setAttribute'])
            ->getMock();

        $model_mock->expects($this->any())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->any())
            ->method('setAttribute')
            ->with('answered', true);

        $rep_mock = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->any())
            ->method('getFirstOrNull')
            ->with($mess->id)
            ->willReturn($model_mock);

        $this->instance(
            MessageRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user)
            ->post(route('admin.message.status-answered'), $data)
            ->assertRedirect(route('admin.messages'));

        $response->assertSessionHas('status_success');
    }

    public function testSetAnsweredStatusModelSaveFailed()
    {
        $mess = Message::factory()->createOne();

        $data = ['id' => $mess->id];

        $model_mock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setAttribute'])
            ->getMock();

        $model_mock->expects($this->any())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->any())
            ->method('setAttribute')
            ->with('answered', true);

        $rep_mock = $this->getMockBuilder(MessageRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->any())
            ->method('getFirstOrNull')
            ->with($mess->id)
            ->willReturn($model_mock);

        $this->instance(
            MessageRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user)
            ->post(route('admin.message.status-answered'), $data)
            ->assertRedirect(route('admin.messages'));

        $response->assertSessionHas('status_failed');
    }

    public function testDeleteAnsweredSuccess()
    {
        $builder_mock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $builder_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->addMethods(['where'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('where')
            ->willReturn($builder_mock);

        $this->instance(
            Message::class,
            $model_mock
        );

        $response = $this->actingAs($this->user)
            ->get(route('admin.messages.clear'))
            ->assertRedirect(route('admin.messages'));

        $response->assertSessionHas('status_success');
    }

    public function testDeleteAnsweredModelsDeleteFailed()
    {
        $builder_mock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $builder_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $model_mock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->addMethods(['where'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('where')
            ->willReturn($builder_mock);

        $this->instance(
            Message::class,
            $model_mock
        );

        $response = $this->actingAs($this->user)
            ->get(route('admin.messages.clear'))
            ->assertRedirect(route('admin.messages'));

        $response->assertSessionHas('status_failed');
    }
}
