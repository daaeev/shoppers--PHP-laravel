<?php

namespace Tests\Feature\Controllers;

use App\Models\Subscribe;
use App\Models\User;
use App\Services\Interfaces\SubscribeRepositoryInterface;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\SubscribeRepository;
use App\Services\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
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
        $model_mock = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $builder_mock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['first'])
            ->getMock();

        $builder_mock->expects($this->once())
            ->method('first')
            ->willReturn($model_mock);

        $model_build_mock = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->addMethods(['where'])
            ->getMock();

        $model_build_mock->expects($this->once())
            ->method('where')
            ->willReturn($builder_mock);

        $this->instance(
            Subscribe::class,
            $model_build_mock
        );

        $response = $this->actingAs($this->user)
            ->get(route('news.unsub'))
            ->assertRedirect(route('profile'));

        $response->assertSessionHas('status_success');
        $response->assertSessionDoesntHaveErrors();
    }

    public function testUnsubUserIfUserNotSub()
    {
        $builder_mock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['first'])
            ->getMock();

        $builder_mock->expects($this->once())
            ->method('first')
            ->willReturn(null);

        $model_build_mock = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->addMethods(['where'])
            ->getMock();

        $model_build_mock->expects($this->once())
            ->method('where')
            ->willReturn($builder_mock);

        $this->instance(
            Subscribe::class,
            $model_build_mock
        );

        $response = $this->actingAs($this->user)
            ->get(route('news.unsub'))
            ->assertRedirect(route('profile'));

        $response->assertSessionHas('status_warning');
        $response->assertSessionDoesntHaveErrors();
    }

    public function testUnsubUserModelDeleteFailed()
    {
        $model_mock = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $builder_mock = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['first'])
            ->getMock();

        $builder_mock->expects($this->once())
            ->method('first')
            ->willReturn($model_mock);

        $model_build_mock = $this->getMockBuilder(Subscribe::class)
            ->disableOriginalConstructor()
            ->addMethods(['where'])
            ->getMock();

        $model_build_mock->expects($this->once())
            ->method('where')
            ->willReturn($builder_mock);

        $this->instance(
            Subscribe::class,
            $model_build_mock
        );

        $response = $this->actingAs($this->user)
            ->get(route('news.unsub'))
            ->assertRedirect(route('profile'));

        $response->assertSessionHas('status_failed');
        $response->assertSessionDoesntHaveErrors();
    }
}
