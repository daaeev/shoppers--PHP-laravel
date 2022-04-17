<?php

namespace Tests\Feature\Controllers;

use App\Models\Subscribe;
use App\Models\User;
use App\Services\Interfaces\SubscribeRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\SubscribeRepository;
use App\Services\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testCreateSubSuccess()
    {
        $model_mock = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with(['email' => 'test@gmail.com', 'user_id' => $this->user->id]);

        $this->instance(
            Subscribe::class,
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

        $this->actingAs($this->user)
            ->post(route('news.sub'), ['email' => 'test@gmail.com'])
            ->assertOk();
    }

    public function testCreateSubSuccessIfAlreadySub()
    {
        $rep_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $this->user->news_subscribe = true;

        $rep_mock->expects($this->any())
            ->method('getAuthenticated')
            ->willReturn($this->user);

        $this->instance(
            UserRepositoryInterface::class,
            $rep_mock
        );

        $this->actingAs($this->user)
            ->post(route('news.sub'), ['email' => 'test@gmail.com'])
            ->assertOk();
    }

    public function testCreateSubModelSaveFailed()
    {
        $model_mock = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with(['email' => 'test@gmail.com', 'user_id' => $this->user->id]);

        $this->instance(
            Subscribe::class,
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

        $this->actingAs($this->user)
            ->post(route('news.sub'), ['email' => 'test@gmail.com'])
            ->assertStatus(500);
    }

    public function testUnsubUserSuccess()
    {
        $sub = Subscribe::factory()->createOne(['user_id' => $this->user->id]);

        $this->assertDatabaseHas(Subscribe::class, ['id' => $sub->id]);

        $this->actingAs($this->user)
            ->get(route('news.unsub'))
            ->assertRedirect(route('home'));

        $this->assertDatabaseMissing(Subscribe::class, ['id' => $sub->id]);
    }

    public function testUnsubUserIfUserNotSub()
    {
        $this->actingAs($this->user)
            ->get(route('news.unsub'))
            ->assertRedirect(route('home'));
    }
}
