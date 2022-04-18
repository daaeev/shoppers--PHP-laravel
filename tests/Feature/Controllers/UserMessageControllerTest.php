<?php

namespace Tests\Feature\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserMessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testCreateSuccess()
    {
        $data = [
            'title' => 'Title',
            'content' => 'Content',
            'email' => 'test@lrvl.app',
            'first_name' => 'Name',
            'last_name' => 'Name',
        ];

        $model_mock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'title' => 'Title',
                'content' => 'Content',
                'email' => 'test@lrvl.app',
                'first_name' => 'Name',
                'last_name' => 'Name',
                'user_id' => $this->user->id,
            ]);

        $this->instance(
            Message::class,
            $model_mock
        );

        $rep_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $rep_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($this->user);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user)
            ->post(route('message.create'), $data)
            ->assertRedirect(route('contact'));

        $response->assertSessionHas('status_success');
    }

    public function testCreateModelSaveFailed()
    {
        $data = [
            'title' => 'Title',
            'content' => 'Content',
            'email' => 'test@lrvl.app',
            'first_name' => 'Name',
            'last_name' => 'Name',
        ];

        $model_mock = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'title' => 'Title',
                'content' => 'Content',
                'email' => 'test@lrvl.app',
                'first_name' => 'Name',
                'last_name' => 'Name',
                'user_id' => $this->user->id,
            ]);

        $this->instance(
            Message::class,
            $model_mock
        );

        $rep_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $rep_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($this->user);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user)
            ->post(route('message.create'), $data)
            ->assertRedirect(route('contact', $model_mock->attributesToArray()));

        $response->assertSessionHas('status_failed');
    }
}
